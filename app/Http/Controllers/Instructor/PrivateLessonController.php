<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\PrivateLesson;
use App\Models\PrivateLessonAvailability;
use App\Models\PrivateLessonBlock;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrivateLessonController extends Controller
{
    private function getInstructor(): Instructor
    {
        return Instructor::where('user_id', auth()->id())->firstOrFail();
    }

    public function index()
    {
        $instructor = $this->getInstructor();

        // Lesson data (from requests())
        $pending = PrivateLesson::where('instructor_id', $instructor->id)
            ->where('status', 'pending')->with(['handler', 'dog'])->latest()->get();
        $upcoming = PrivateLesson::where('instructor_id', $instructor->id)
            ->where('status', 'confirmed')
            ->where('requested_date', '>=', Carbon::today())
            ->with(['handler', 'dog'])->orderBy('requested_date')->orderBy('requested_start_time')->get();
        $past = PrivateLesson::where('instructor_id', $instructor->id)
            ->whereIn('status', ['completed', 'cancelled', 'reschedule_requested'])
            ->with(['handler', 'dog'])->latest()->get();

        // Availability data (from availability())
        $instructor->load(['privateLessonAvailabilities', 'privateLessonBlocks']);

        $tab = request('tab', $pending->count() > 0 ? 'pending' : ($upcoming->count() > 0 ? 'upcoming' : 'availability'));

        return view('instructor.private-lessons.index', compact('instructor', 'pending', 'upcoming', 'past', 'tab'));
    }

    public function availability(Request $request)
    {
        $instructor = $this->getInstructor();

        if ($request->isMethod('post')) {
            $request->validate([
                'slots'               => 'nullable|array',
                'slots.*.day_of_week' => 'required|integer|min:0|max:6',
                'slots.*.start_time'  => 'required|date_format:H:i',
            ]);

            // Replace all availabilities
            $instructor->privateLessonAvailabilities()->delete();
            foreach ($request->input('slots', []) as $slot) {
                $instructor->privateLessonAvailabilities()->create([
                    'day_of_week' => $slot['day_of_week'],
                    'start_time'  => $slot['start_time'],
                ]);
            }

            return back()->with('success', 'Availability saved.');
        }

        $instructor->load(['privateLessonAvailabilities', 'privateLessonBlocks']);
        return view('instructor.private-lessons.availability', compact('instructor'));
    }

    public function toggleOptIn(Request $request)
    {
        $instructor = $this->getInstructor();
        $instructor->update([
            'private_lessons_enabled' => !$instructor->private_lessons_enabled,
        ]);
        return back()->with('success', $instructor->private_lessons_enabled ? 'Private lessons enabled.' : 'Private lessons disabled.');
    }

    public function requests()
    {
        $instructor = $this->getInstructor();

        $pending = PrivateLesson::where('instructor_id', $instructor->id)
            ->where('status', 'pending')
            ->with(['handler', 'dog'])
            ->latest()
            ->get();

        $upcoming = PrivateLesson::where('instructor_id', $instructor->id)
            ->where('status', 'confirmed')
            ->where('requested_date', '>=', Carbon::today())
            ->with(['handler', 'dog'])
            ->orderBy('requested_date')
            ->orderBy('requested_start_time')
            ->get();

        $past = PrivateLesson::where('instructor_id', $instructor->id)
            ->whereIn('status', ['completed', 'cancelled', 'reschedule_requested'])
            ->with(['handler', 'dog'])
            ->latest()
            ->get();

        return view('instructor.private-lessons.requests', compact('instructor', 'pending', 'upcoming', 'past'));
    }

    public function confirm(PrivateLesson $lesson)
    {
        $instructor = $this->getInstructor();
        abort_if($lesson->instructor_id !== $instructor->id, 403);

        $lesson->update([
            'status'                => 'confirmed',
            'confirmed_date'        => $lesson->requested_date,
            'confirmed_start_time'  => $lesson->requested_start_time,
        ]);

        $lesson->load('handler.user', 'instructor', 'dog');
        if ($lesson->handler?->user) {
            $dogName = $lesson->dog->name;
            $date    = $lesson->requested_date->format('d M Y');
            $time    = Carbon::parse($lesson->requested_start_time)->format('g:i A');
            app(MessageService::class)->createDirect(
                auth()->id(),
                $lesson->handler->user->id,
                "Private Lesson Confirmed — {$dogName}",
                [['type' => 'text', 'content' => "Your private lesson for {$dogName} has been confirmed for {$date} at {$time}. See you there!"]]
            );
        }

        return back()->with('success', 'Lesson confirmed.');
    }

    public function reject(Request $request, PrivateLesson $lesson)
    {
        $instructor = $this->getInstructor();
        abort_if($lesson->instructor_id !== $instructor->id, 403);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $lesson->update([
            'status'          => 'cancelled',
            'reschedule_note' => $request->input('reason'),
        ]);

        $lesson->load('handler.user', 'instructor', 'dog');
        if ($lesson->handler?->user) {
            $dogName = $lesson->dog->name;
            $date    = $lesson->requested_date->format('d M Y');
            $body    = "Unfortunately your private lesson request for {$dogName} on {$date} was not able to be confirmed.";
            if ($request->filled('reason')) {
                $body .= ' ' . $request->input('reason');
            }
            app(MessageService::class)->createDirect(
                auth()->id(),
                $lesson->handler->user->id,
                "Private Lesson Request — {$dogName}",
                [['type' => 'text', 'content' => $body]]
            );
        }

        return back()->with('success', 'Lesson rejected.');
    }

    public function requestReschedule(Request $request, PrivateLesson $lesson)
    {
        $instructor = $this->getInstructor();
        abort_if($lesson->instructor_id !== $instructor->id, 403);

        $request->validate([
            'reschedule_note' => 'required|string|max:500',
        ]);

        $lesson->update([
            'status'          => 'reschedule_requested',
            'reschedule_note' => $request->input('reschedule_note'),
        ]);

        $lesson->load('handler.user', 'instructor', 'dog');
        if ($lesson->handler?->user) {
            $dogName = $lesson->dog->name;
            $date    = $lesson->requested_date->format('d M Y');
            $note    = $request->input('reschedule_note');
            app(MessageService::class)->createDirect(
                auth()->id(),
                $lesson->handler->user->id,
                "Private Lesson Reschedule Request — {$dogName}",
                [
                    ['type' => 'text', 'content' => "Your instructor has requested to reschedule your lesson for {$dogName} originally booked on {$date}. Reason: {$note}. Please re-book at a convenient time."],
                    ['type' => 'button', 'label' => 'Re-book a Lesson', 'url' => url('/my/private-lessons/book')],
                ]
            );
        }

        return back()->with('success', 'Reschedule request sent.');
    }

    public function complete(Request $request, PrivateLesson $lesson)
    {
        $instructor = $this->getInstructor();
        abort_if($lesson->instructor_id !== $instructor->id, 403);

        $request->validate([
            'instructor_notes' => 'nullable|string|max:2000',
        ]);

        $lesson->update([
            'status'           => 'completed',
            'instructor_notes' => $request->input('instructor_notes'),
        ]);

        // Send session notes to handler if notes were provided
        if ($request->filled('instructor_notes') && $lesson->handler?->user) {
            app(MessageService::class)->createDirect(
                auth()->id(),
                $lesson->handler->user->id,
                'Private Lesson Notes — ' . $lesson->dog->name,
                [['type' => 'text', 'content' => $request->input('instructor_notes')]]
            );
        }

        return back()->with('success', 'Lesson marked as complete.');
    }

    public function storeBlock(Request $request)
    {
        $instructor = $this->getInstructor();

        $request->validate([
            'blocked_date' => 'required|date',
            'reason'       => 'nullable|string|max:200',
        ]);

        $instructor->privateLessonBlocks()->create([
            'blocked_date' => $request->input('blocked_date'),
            'reason'       => $request->input('reason'),
        ]);

        return back()->with('success', 'Blocked date added.');
    }

    public function deleteBlock(PrivateLessonBlock $block)
    {
        $instructor = $this->getInstructor();
        abort_if($block->instructor_id !== $instructor->id, 403);

        $block->delete();
        return back()->with('success', 'Blocked date removed.');
    }
}
