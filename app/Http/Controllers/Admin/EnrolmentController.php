<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\{ClassAssigned, EnrolmentConfirmed, EnrolmentRejected, VetClearanceRequest};
use App\Models\{Dog, DogClass, Enrolment};
use App\Services\{InvoicesOnlineService, MessageService, PushNotificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EnrolmentController extends Controller
{
    public function show(Enrolment $enrolment)
    {
        $enrolment->load(['handler.user', 'handler.accountHolder', 'dog.assessmentRequests.scores', 'dogClass', 'goals']);
        $availableClasses = DogClass::whereNotNull('start_date')
            ->where('end_date', '>=', now())
            ->orderBy('start_date')
            ->get();
        return view('admin.enrolments.show', compact('enrolment', 'availableClasses'));
    }

    public function confirm(Enrolment $enrolment)
    {
        $enrolment->load(['handler.user', 'dog', 'dogClass']);

        $enrolment->update([
            'status'       => $enrolment->class_id ? 'confirmed' : 'pending_class_assignment',
            'confirmed_at' => now(),
        ]);

        $handler = $enrolment->handler;
        $handler->update(['status' => 'active']);
        $handler->user->update(['is_active' => true]);

        Mail::to($handler->user->email)->send(new EnrolmentConfirmed($enrolment));

        // Send welcome inbox message
        if ($enrolment->class_id) {
            $dogClass = $enrolment->dogClass->load('instructors');
            app(MessageService::class)->sendTemplateToHandler(
                'class_confirmation',
                $handler->user,
                ['handler' => $handler, 'dog' => $enrolment->dog, 'class' => $dogClass],
                auth()->id(),
                $enrolment->class_id
            );
        }

        // Fire invoice for term classes on confirmation
        if ($enrolment->class_id) {
            $dogClass  = $enrolment->dogClass->load('classType');
            $classType = $dogClass->classType;

            if ($classType?->duration_type === 'term' && $classType->io_prod_code) {
                $io      = new InvoicesOnlineService();
                $price   = (float) ($dogClass->classType?->course_price ?? $dogClass->course_price ?? 0);
                $discount = $enrolment->dog?->multi_dog_discount ? 0.75 : 1.0;

                $io->createInvoice($handler, [[
                    'prod_code'   => $classType->io_prod_code,
                    'qty'         => 1,
                    'description' => $dogClass->name . ' — ' . ($enrolment->dog?->name ?? 'Dog'),
                    'amount'      => $price * $discount,
                ]], true);
            }
        }

        $msg = $enrolment->class_id
            ? 'Enrolment confirmed and welcome message sent.'
            : 'Enrolment confirmed. No class assigned yet — flagged as pending class assignment.';

        return back()->with('success', $msg);
    }

    public function requestVetClearance(Enrolment $enrolment)
    {
        $enrolment->load(['handler.user', 'dog', 'dogClass']);

        $enrolment->update([
            'status'                      => 'vet_clearance_requested',
            'vet_clearance_requested_at'  => now(),
        ]);

        Mail::to($enrolment->handler->user->email)->send(new VetClearanceRequest($enrolment));

        app(PushNotificationService::class)->sendToUser(
            $enrolment->handler->user,
            'Vet Clearance Required',
            'Please upload a vet clearance certificate to complete your enrolment.',
            ['url' => '/my']
        );

        return back()->with('success', 'Vet clearance email sent to ' . $enrolment->handler->first_name . '.');
    }

    public function reject(Request $request, Enrolment $enrolment)
    {
        $request->validate(['rejection_reason' => 'required|string|min:5']);

        $enrolment->load(['handler.user', 'dog', 'dogClass']);

        $enrolment->update([
            'status'           => 'withdrawn',
            'rejection_reason' => $request->rejection_reason,
        ]);

        Mail::to($enrolment->handler->user->email)
            ->send(new EnrolmentRejected($enrolment, $request->rejection_reason));

        app(PushNotificationService::class)->sendToUser(
            $enrolment->handler->user,
            'Enrolment Update',
            'There has been an update regarding your McKaynine enrolment. Please check your email.',
            ['url' => '/my']
        );

        return redirect()->route('admin.enrolments.index')->with('success', 'Enrolment rejected and notification sent.');
    }

    public function assignClass(Request $request, Enrolment $enrolment)
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);

        $enrolment->update([
            'class_id' => $request->class_id,
            'status'   => 'confirmed',
        ]);

        $enrolment->load(['handler.user', 'dog', 'dogClass.scheduledDates', 'dogClass.instructors']);
        Mail::to($enrolment->handler->user->email)->send(new ClassAssigned($enrolment));

        $messageService = app(MessageService::class);

        $messageService->sendTemplateToHandler(
            'class_confirmation',
            $enrolment->handler->user,
            ['handler' => $enrolment->handler, 'dog' => $enrolment->dog, 'class' => $enrolment->dogClass],
            auth()->id(),
            $enrolment->class_id
        );

        $messageService->backfillContentForEnrolment($enrolment);

        return back()->with('success', 'Class assigned — confirmation email and inbox message sent.');
    }

    public function confirmClass(Enrolment $enrolment)
    {
        $enrolment->load(['handler.user', 'dog', 'dogClass.scheduledDates', 'dogClass.instructors']);

        $enrolment->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        Mail::to($enrolment->handler->user->email)->send(new ClassAssigned($enrolment));

        $messageService = app(MessageService::class);

        $messageService->sendTemplateToHandler(
            'class_confirmation',
            $enrolment->handler->user,
            ['handler' => $enrolment->handler, 'dog' => $enrolment->dog, 'class' => $enrolment->dogClass],
            auth()->id(),
            $enrolment->class_id
        );

        $messageService->backfillContentForEnrolment($enrolment);

        return back()->with('success', 'Class confirmed — email and inbox message sent to ' . $enrolment->handler->first_name . '.');
    }

    public function assignInstructor(Request $request, Enrolment $enrolment)
    {
        $request->validate([
            'assigned_instructor_id' => 'nullable|exists:instructors,id',
        ]);

        $enrolment->update([
            'assigned_instructor_id' => $request->assigned_instructor_id ?: null,
        ]);

        return back()->with('success', 'Instructor assigned.');
    }

    public function storeForDog(Request $request, Dog $dog)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'status'   => 'required|in:pending,confirmed',
        ]);

        Enrolment::create([
            'dog_id'      => $dog->id,
            'handler_id'  => $dog->handler_id,
            'class_id'    => $request->class_id,
            'status'      => $request->status,
            'enrolled_at' => now(),
        ]);

        return back()->with('success', 'Enrolment created.');
    }

    public function index()
    {
        $classConfirmations = Enrolment::with(['handler', 'dog', 'dogClass'])
            ->where('pathway', 'existing')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $enrolments = Enrolment::with(['handler', 'dog', 'dogClass'])
            ->where(fn($q) => $q->where('pathway', '!=', 'existing')->orWhereNull('pathway'))
            ->whereNotIn('status', ['completed', 'withdrawn'])
            ->orderByRaw("CASE status
                WHEN 'pending' THEN 1
                WHEN 'vet_clearance_review' THEN 2
                WHEN 'vet_clearance_requested' THEN 3
                WHEN 'pending_class_assignment' THEN 4
                ELSE 5 END")
            ->latest()
            ->paginate(30);

        return view('admin.enrolments.index', compact('enrolments', 'classConfirmations'));
    }
}
