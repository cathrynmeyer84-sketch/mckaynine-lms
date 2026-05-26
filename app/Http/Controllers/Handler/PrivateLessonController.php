<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\BranchSetting;
use App\Models\CalendarDay;
use App\Models\CalendarWeek;
use App\Models\Instructor;
use App\Models\PrivateLesson;
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrivateLessonController extends Controller
{
    private function getHandler()
    {
        return auth()->user()->handler;
    }

    public function index()
    {
        $handler = $this->getHandler();

        $upcoming = PrivateLesson::where('handler_id', $handler->id)
            ->whereIn('status', ['pending', 'confirmed', 'reschedule_requested'])
            ->with(['instructor', 'dog'])
            ->orderBy('requested_date')
            ->orderBy('requested_start_time')
            ->get();

        $past = PrivateLesson::where('handler_id', $handler->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['instructor', 'dog'])
            ->latest()
            ->get();

        return view('handler.private-lessons.index', compact('upcoming', 'past'));
    }

    public function browse()
    {
        // If the handler arrived via an assessment outcome email link but wasn't
        // logged in at the time, send the in-app invite now that they are.
        if ($arId = session('private_lesson_ar_id')) {
            $ar = \App\Models\AssessmentRequest::find($arId);
            if ($ar) {
                app(\App\Http\Controllers\EnrolmentController::class)->sendPrivateLessonInvite($ar);
            }
            session()->forget('private_lesson_ar_id');
        }

        $instructors = Instructor::where('private_lessons_enabled', true)
            ->with('user')
            ->get();

        return view('handler.private-lessons.book', compact('instructors'));
    }

    public function slots(Instructor $instructor)
    {
        $instructor->load(['privateLessonAvailabilities', 'privateLessonBlocks']);

        $result = [];
        for ($i = 1; $i <= 28; $i++) {
            $date      = Carbon::today()->addDays($i);
            $dayOfWeek = $date->dayOfWeek; // 0=Sun, 6=Sat

            $daySlots = $instructor->privateLessonAvailabilities
                ->where('day_of_week', $dayOfWeek);

            if ($daySlots->isEmpty()) continue;

            // Skip dates blocked in the admin calendar (whole week or specific day)
            if (!CalendarWeek::isWeekActive($date)) continue;
            if (!CalendarDay::isDayActive($date)) continue;

            // Skip instructor-specific blocked dates
            $isBlocked = $instructor->privateLessonBlocks
                ->where('blocked_date', $date->toDateString())->isNotEmpty();
            if ($isBlocked) continue;

            // Get existing bookings for this date
            $booked = PrivateLesson::where('instructor_id', $instructor->id)
                ->where('requested_date', $date->toDateString())
                ->whereIn('status', ['pending', 'confirmed'])
                ->pluck('requested_start_time')
                ->toArray();

            $available = [];
            foreach ($daySlots as $slot) {
                if (!in_array($slot->start_time, $booked)) {
                    $available[] = [
                        'start_time'       => $slot->start_time,
                        'start_time_label' => Carbon::parse($slot->start_time)->format('g:i A'),
                    ];
                }
            }

            if (!empty($available)) {
                $result[] = [
                    'date'       => $date->toDateString(),
                    'date_label' => $date->format('l, d M Y'),
                    'slots'      => $available,
                ];
            }
        }

        return response()->json($result)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function store(Request $request)
    {
        $handler = $this->getHandler();

        $data = $request->validate([
            'instructor_id'        => 'required|integer|exists:instructors,id',
            'dog_id'               => 'required|integer|exists:dogs,id',
            'requested_date'       => 'required|date|after:today',
            'requested_start_time' => 'required|date_format:H:i',
            'handler_notes'        => 'nullable|string|max:500',
        ]);

        // Reject if the chosen date falls on an admin-blocked day or week
        $chosenDate = Carbon::parse($data['requested_date']);
        if (!CalendarWeek::isWeekActive($chosenDate) || !CalendarDay::isDayActive($chosenDate)) {
            return back()->withErrors(['requested_date' => 'That date is not available for bookings.'])->withInput();
        }

        $fee = BranchSetting::current()->private_lesson_fee;

        $lesson = PrivateLesson::create([
            'handler_id'           => $handler->id,
            'dog_id'               => $data['dog_id'],
            'instructor_id'        => $data['instructor_id'],
            'requested_date'       => $data['requested_date'],
            'requested_start_time' => $data['requested_start_time'],
            'handler_notes'        => $data['handler_notes'] ?? null,
            'status'               => 'pending',
            'fee'                  => $fee,
        ]);

        // Notify instructor
        $lesson->load('instructor.user', 'dog', 'handler');
        if ($lesson->instructor?->user) {
            app(MessageService::class)->createDirect(
                auth()->id(),
                $lesson->instructor->user->id,
                'New Private Lesson Request — ' . $lesson->dog->name,
                [['type' => 'text', 'content' => $handler->first_name . ' ' . $handler->last_name . ' has requested a private lesson for ' . $lesson->dog->name . ' on ' . $lesson->requested_date->format('d M Y') . ' at ' . Carbon::parse($lesson->requested_start_time)->format('g:i A') . '.']]
            );
        }

        return redirect()->route('handler.private-lessons.index')
            ->with('success', 'Lesson request submitted! Your instructor will confirm shortly.');
    }

    public function cancel(PrivateLesson $lesson)
    {
        $handler = $this->getHandler();
        abort_if($lesson->handler_id !== $handler->id, 403);

        $lesson->update(['status' => 'cancelled']);
        return back()->with('success', 'Lesson cancelled.');
    }
}
