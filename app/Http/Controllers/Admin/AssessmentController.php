<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AssessmentBookingInvite;
use App\Mail\AssessmentOutcome;
use App\Models\{AppSetting, AssessmentAvailability, AssessmentRequest, AssessmentSlot, AssessmentScore, AssessmentSpecialDate};
use App\Models\CalendarDay;
use App\Models\CalendarWeek;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AssessmentController extends Controller
{
    public function index()
    {
        $pending     = AssessmentRequest::where('status', 'pending')->with(['handler', 'dog'])->latest()->get();
        $offered     = AssessmentRequest::where('status', 'slot_offered')->with(['handler', 'dog'])->latest()->get();
        $booked      = AssessmentRequest::where('status', 'booked')->with(['handler', 'dog', 'slot'])->latest()->get();
        $slots       = $this->generateSlotPreview(28); // next 4 weeks for sidebar

        $completed = AssessmentRequest::where('status', 'completed')
            ->with(['handler.enrolments', 'dog', 'scores'])
            ->latest()
            ->get()
            ->groupBy(fn($a) => $a->created_at->format('F Y'));

        return view('admin.assessments.index', compact('pending', 'offered', 'booked', 'completed', 'slots'));
    }

    public function show(AssessmentRequest $assessmentRequest)
    {
        $assessmentRequest->load(['handler', 'dog', 'slot', 'scores.evaluator']);
        return view('admin.assessments.show', compact('assessmentRequest'));
    }

    public function sendBookingLink(AssessmentRequest $assessmentRequest)
    {
        $email = $assessmentRequest->handler?->user?->email;
        if (!$email) {
            return back()->with('error', 'No email address found for this handler.');
        }

        $bookingUrl = URL::signedRoute('enrol.public-slot', ['assessmentRequest' => $assessmentRequest->id]);

        Mail::to($email)->send(new AssessmentBookingInvite($assessmentRequest, $bookingUrl));

        $assessmentRequest->update(['status' => 'slot_offered']);

        return back()->with('success', "Booking link sent to {$email}.");
    }

    public function manageSlots()
    {
        $availabilities = AssessmentAvailability::orderBy('day_of_week')->orderBy('start_time')->get();
        $specialDates   = AssessmentSpecialDate::where('date', '>=', today())->orderBy('date')->orderBy('start_time')->get();
        $preview        = $this->generateSlotPreview(56); // 8-week preview for admin
        return view('admin.assessments.slots', compact('availabilities', 'specialDates', 'preview'));
    }

    public function storeAvailability(Request $request)
    {
        $request->validate([
            'day_of_week'  => 'required|integer|min:0|max:6',
            'start_time'   => 'required|date_format:H:i',
            'max_bookings' => 'required|integer|min:1|max:20',
        ]);
        AssessmentAvailability::create($request->only(['day_of_week', 'start_time', 'max_bookings', 'notes']));
        return back()->with('success', 'Recurring slot added.');
    }

    public function deleteAvailability(AssessmentAvailability $availability)
    {
        $availability->delete();
        return back()->with('success', 'Recurring slot removed.');
    }

    public function storeSpecialDate(Request $request)
    {
        $request->validate([
            'date'         => 'required|date|after:today',
            'start_time'   => 'required|date_format:H:i',
            'max_bookings' => 'required|integer|min:1|max:20',
        ]);
        AssessmentSpecialDate::create($request->only(['date', 'start_time', 'max_bookings', 'notes']));
        return back()->with('success', 'Special date added.');
    }

    public function deleteSpecialDate(AssessmentSpecialDate $specialDate)
    {
        $specialDate->delete();
        return back()->with('success', 'Special date removed.');
    }

    /**
     * Generate a preview of upcoming available slots (used by admin and handler views).
     */
    public function generateSlotPreview(int $days = 42): \Illuminate\Support\Collection
    {
        $slots          = collect();
        $availabilities = AssessmentAvailability::all();
        $specials       = AssessmentSpecialDate::where('date', '>', today())
            ->where('date', '<=', today()->addDays($days))
            ->orderBy('date')->get();

        // Weekly recurring
        for ($i = 1; $i <= $days; $i++) {
            $date = today()->addDays($i);
            if (!CalendarWeek::isWeekActive($date)) continue;
            if (!CalendarDay::isDayActive($date)) continue;

            foreach ($availabilities as $avail) {
                if ($avail->day_of_week == $date->dayOfWeek) {
                    $booked = AssessmentRequest::whereIn('status', ['booked', 'completed'])
                        ->whereHas('slot', fn($q) => $q->whereDate('date', $date->toDateString())->where('start_time', $avail->start_time))
                        ->count();
                    $slots->push((object)[
                        'date'         => $date->copy(),
                        'start_time'   => $avail->start_time,
                        'max_bookings' => $avail->max_bookings,
                        'notes'        => $avail->notes,
                        'booked'       => $booked,
                        'remaining'    => max(0, $avail->max_bookings - $booked),
                        'source'       => 'recurring',
                        'key'          => $date->format('Y-m-d') . '|' . $avail->start_time,
                    ]);
                }
            }
        }

        // Special one-off dates
        foreach ($specials as $special) {
            if (!CalendarWeek::isWeekActive($special->date)) continue;
            if (!CalendarDay::isDayActive($special->date)) continue;

            // Don't duplicate if recurring already covers this date+time
            if ($slots->contains(fn($s) => $s->date->isSameDay($special->date) && $s->start_time === $special->start_time)) {
                continue;
            }

            $booked = AssessmentRequest::whereIn('status', ['booked', 'completed'])
                ->whereHas('slot', fn($q) => $q->whereDate('date', $special->date->toDateString())->where('start_time', $special->start_time))
                ->count();
            $slots->push((object)[
                'date'         => $special->date,
                'start_time'   => $special->start_time,
                'max_bookings' => $special->max_bookings,
                'notes'        => $special->notes,
                'booked'       => $booked,
                'remaining'    => max(0, $special->max_bookings - $booked),
                'source'       => 'special',
                'key'          => $special->date->format('Y-m-d') . '|' . $special->start_time,
            ]);
        }

        return $slots->sortBy(fn($s) => $s->date->format('Y-m-d') . ' ' . $s->start_time)->values();
    }

    public function settings()
    {
        $settings = AppSetting::whereIn('key', [
                'admin_email', 'assessment_location', 'assessment_instructions',
            ])
            ->pluck('value', 'key')
            ->toArray();
        return view('admin.assessments.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->input('settings', []) as $key => $value) {
            AppSetting::set($key, $value ?: null);
        }
        return back()->with('success', 'Settings saved.');
    }

    public function scoreForm(AssessmentRequest $assessmentRequest)
    {
        $assessmentRequest->load(['handler', 'dog']);
        $existingScore = $assessmentRequest->scores;
        $availableClassTypes = \App\Models\ClassType::where('info_page_enabled', true)
            ->where('page_template', '!=', 'puppy')
            ->orderBy('name')
            ->get();
        return view('admin.assessments.score', compact('assessmentRequest', 'existingScore', 'availableClassTypes'));
    }

    public function storeScore(Request $request, AssessmentRequest $assessmentRequest)
    {
        $score = AssessmentScore::updateOrCreate(
            ['assessment_request_id' => $assessmentRequest->id],
            array_merge(
                $request->only([
                    'step1_score','step1_notes',
                    'step2_score','step2_notes',
                    'step3_score','step3_notes',
                    'step4_score','step4_notes',
                    'step5_score','step5_notes',
                    'step6_score','step6_notes',
                    'step7_score','step7_notes',
                    'step7_skipped','step7_skip_reason',
                    'final_outcome','override_reason','global_notes',
                    'recommended_class_name','recommended_class_url','recommended_class_ids',
                ]),
                ['evaluator_id' => auth()->id(), 'status' => 'submitted', 'submitted_at' => now()]
            )
        );
        $score->recommended_outcome = $score->calculateRecommendedOutcome();
        if (!$request->final_outcome) $score->final_outcome = $score->recommended_outcome;
        $score->save();
        $assessmentRequest->update(['status' => 'completed']);

        if ($request->boolean('send_outcome_email')) {
            $email = $assessmentRequest->handler?->user?->email;
            if ($email) {
                Mail::to($email)->send(new AssessmentOutcome($assessmentRequest, $score));
            }
        }

        return redirect()->route('admin.assessments.index')->with('success', 'Assessment scored and submitted.');
    }

    public function releaseOutcome(AssessmentScore $assessmentScore)
    {
        $assessmentScore->update(['status' => 'outcome_sent']);
        return back()->with('success', 'Outcome released to handler.');
    }
}
