<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{AppSetting, CalendarDay, DogClass, ClassDate};
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // All off-day records for this year, keyed by date string
        $offDays = CalendarDay::whereYear('date', $year)
            ->get()
            ->keyBy(fn($d) => $d->date->toDateString());

        // Build 12-month calendar structure with explicit week rows
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $first  = Carbon::create($year, $m, 1);
            $last   = $first->copy()->endOfMonth();
            // Sunday-first week: Sun=0 … Sat=6
            $offset = $first->dayOfWeek;

            // Build days list
            $dayCells = [];
            for ($d = 1; $d <= $last->day; $d++) {
                $date   = Carbon::create($year, $m, $d);
                $key    = $date->toDateString();
                $record = $offDays->get($key);
                $dayCells[] = [
                    'date'   => $key,
                    'day'    => $d,
                    'active' => $record ? $record->is_active : true,
                    'label'  => $record?->label ?? '',
                ];
            }

            // Split into week rows of 7, padding start and end with nulls
            $padded = array_merge(array_fill(0, $offset, null), $dayCells);
            $remainder = count($padded) % 7;
            if ($remainder > 0) {
                $padded = array_merge($padded, array_fill(0, 7 - $remainder, null));
            }
            $weeks = array_chunk($padded, 7);

            $months[] = [
                'name'  => $first->format('F'),
                'weeks' => $weeks,
            ];
        }

        $settings = AppSetting::whereIn('key', ['off_day_email_subject', 'off_day_email_body', 'off_day_reminder_days'])
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.calendar.index', compact('year', 'months', 'settings'));
    }

    public function saveDay(Request $request)
    {
        $request->validate([
            'date'      => 'required|date',
            'is_active' => 'required|boolean',
            'label'     => 'nullable|string|max:150',
        ]);

        $date = Carbon::parse($request->date)->toDateString();

        $day = CalendarDay::whereDate('date', $date)->first();
        if ($day) {
            $day->update([
                'is_active'     => $request->boolean('is_active'),
                'label'         => $request->label ?: null,
                'reminder_sent' => false,
            ]);
        } else {
            $day = CalendarDay::create([
                'date'          => $date,
                'is_active'     => $request->boolean('is_active'),
                'label'         => $request->label ?: null,
                'reminder_sent' => false,
            ]);
        }

        // Regenerate dates for all active/upcoming classes spanning this date
        $affected = DogClass::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();

        foreach ($affected as $class) {
            $this->generateClassDates($class);
        }

        return response()->json(['ok' => true]);
    }

    public function saveSettings(Request $request)
    {
        foreach (['off_day_email_subject', 'off_day_email_body', 'off_day_reminder_days'] as $key) {
            AppSetting::set($key, $request->input("settings.$key") ?: null);
        }
        return back()->with('success', 'Settings saved.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function generateClassDates(DogClass $class): int
    {
        if (!$class->start_date || !$class->end_date) return 0;

        $period = CarbonPeriod::create($class->start_date, '1 day', $class->end_date);

        // Only include dates that match the class's day of week
        $classDow = $class->start_date->dayOfWeek; // 0=Sun … 6=Sat

        ClassDate::where('class_id', $class->id)->delete();

        $weekNum = 0;
        $count   = 0;
        $lastActive = null;

        foreach ($period as $date) {
            if ($date->dayOfWeek !== $classDow) continue;

            $isActive = CalendarDay::isDayActive($date);
            if ($isActive) $weekNum++;

            // Content sends 1 hour after class ends
            $contentSendDate = null;
            if ($isActive && $class->end_time) {
                $contentSendDate = $date->copy()->setTimeFromTimeString($class->end_time)->addHour();
            }

            ClassDate::create([
                'class_id'          => $class->id,
                'date'              => $date,
                'start_time'        => $class->start_time ?? null,
                'end_time'          => $class->end_time ?? null,
                'is_off_week'       => !$isActive,
                'off_week_reason'   => $isActive ? null : (CalendarDay::getLabel($date) ?? 'Scheduled break'),
                'week_number'       => $isActive ? $weekNum : null,
                'content_send_date' => $contentSendDate,
            ]);
            $count++;
        }

        return $count;
    }

}
