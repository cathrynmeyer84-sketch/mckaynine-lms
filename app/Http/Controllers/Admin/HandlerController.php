<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Handler, Enrolment, DogClass};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class HandlerController extends Controller
{
    public function index(Request $request)
    {
        $query = Handler::with(['user', 'dogs', 'enrolments.dogClass']);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('cell_number', 'like', "%{$request->search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        $handlers = $query->latest()->paginate(20);
        return view('admin.handlers.index', compact('handlers'));
    }

    public function show(Handler $handler)
    {
        $handler->load(['user', 'dogs', 'enrolments.dogClass', 'enrolments.dog', 'assessmentRequests.dog', 'accountHolder.linkedHandler']);
        $availableClasses = DogClass::where('end_date', '>=', now())->whereNotNull('start_date')->orderBy('start_date')->get();
        return view('admin.handlers.show', compact('handler', 'availableClasses'));
    }

    public function updateStatus(Handler $handler, Request $request)
    {
        $handler->update(['status' => $request->status]);
        return back()->with('success', 'Handler status updated.');
    }

    public function assignClassForm(Handler $handler)
    {
        $availableClasses = DogClass::where('end_date', '>=', now())->whereNotNull('start_date')->with('instructors')->orderBy('start_date')->get();
        return view('admin.handlers.assign-class', compact('handler', 'availableClasses'));
    }

    public function assignClass(Handler $handler, Request $request)
    {
        $request->validate(['dog_id' => 'required', 'class_id' => 'required']);
        $enrolment = Enrolment::where('dog_id', $request->dog_id)->where('class_id', $request->class_id)->first();
        if ($enrolment) {
            $enrolment->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        } else {
            Enrolment::create([
                'dog_id' => $request->dog_id, 'handler_id' => $handler->id,
                'class_id' => $request->class_id, 'status' => 'confirmed',
                'pathway' => 'puppy', 'enrolled_at' => now(), 'confirmed_at' => now(),
            ]);
        }
        $handler->update(['status' => 'active']);
        $user = $handler->user;
        $user->update(['is_active' => true]);
        Password::sendResetLink(['email' => $user->email]);
        return redirect()->route('admin.handlers.show', $handler)->with('success', 'Handler assigned to class and password setup email sent.');
    }

    public function confirmEnrolment(Enrolment $enrolment)
    {
        $enrolment->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        $handler = $enrolment->handler;
        $handler->update(['status' => 'active']);
        $user = $handler->user;
        $user->update(['is_active' => true]);
        Password::sendResetLink(['email' => $user->email]);
        return back()->with('success', 'Enrolment confirmed and password setup email sent.');
    }

    public function rejectEnrolment(Enrolment $enrolment)
    {
        $enrolment->update(['status' => 'withdrawn']);
        return back()->with('success', 'Enrolment rejected.');
    }
}
