<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\{CalendarDay, ClassDate, Enrolment, SchoolYear};
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $handler = auth()->user()->handler;
        $today   = now()->startOfDay();

        // All school years ordered by start date
        $allYears = SchoolYear::orderBy('start_date')->get();

        // Resolve which year to display — URL param takes priority, then current/upcoming
        $schoolYear = $allYears->firstWhere('id', (int) $request->get('year'))
            ?? SchoolYear::current();

        $schoolStart = $schoolYear?->start_date;
        $schoolEnd   = $schoolYear?->end_date;

        // Prev / next year for navigation
        $prevYear = $schoolYear
            ? $allYears->filter(fn($y) => $y->start_date->lt($schoolYear->start_date))->last()
            : null;
        $nextYear = $schoolYear
            ? $allYears->filter(fn($y) => $y->start_date->gt($schoolYear->start_date))->first()
            : null;

        // Display range: school year if set, otherwise 12 rolling months from today
        $startOfRange = ($schoolStart ?? $today->copy())->copy()->startOfMonth();
        $endOfRange   = ($schoolEnd   ?? $today->copy()->addMonths(11))->copy()->endOfMonth();

        // All off days in range, keyed by date string
        $offDays = CalendarDay::where('is_active', false)
            ->whereBetween('date', [$startOfRange->toDateString(), $endOfRange->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($d) => $d->date->toDateString());

        // Handler's confirmed enrolments with class info
        $enrolments = $handler
            ? Enrolment::where('handler_id', $handler->id)
                ->where('status', 'confirmed')
                ->with(['dogClass', 'dog'])
                ->get()
            : collect();

        // Build a map: date string → array of classes scheduled that day for this handler
        $myClassDates = [];
        foreach ($enrolments as $enrolment) {
            $dates = ClassDate::where('class_id', $enrolment->class_id)
                ->whereBetween('date', [$startOfRange->toDateString(), $endOfRange->toDateString()])
                ->orderBy('date')
                ->get();

            foreach ($dates as $cd) {
                $key = $cd->date->toDateString();
                $myClassDates[$key][] = [
                    'class_name'  => $enrolment->dogClass?->name ?? 'Class',
                    'dog_name'    => $enrolment->dog?->name ?? '',
                    'is_off_week' => $cd->is_off_week,
                ];
            }
        }

        // Build month grids covering the full display range
        $months      = [];
        $monthCursor = $startOfRange->copy()->startOfMonth();

        while ($monthCursor->lte($endOfRange)) {
            $first  = $monthCursor->copy();
            $last   = $first->copy()->endOfMonth();
            $offset = $first->dayOfWeek; // Sunday = 0

            $dayCells = [];
            for ($d = 1; $d <= $last->day; $d++) {
                $date        = Carbon::create($first->year, $first->month, $d);
                $key         = $date->toDateString();
                $outsideYear = ($schoolStart && $date->lt($schoolStart)) || ($schoolEnd && $date->gt($schoolEnd));

                $dayCells[] = [
                    'date'         => $key,
                    'day'          => $d,
                    'is_past'      => $date->lt($today),
                    'is_today'     => $date->isToday(),
                    'is_off'       => isset($offDays[$key]),
                    'off_label'    => $offDays[$key]?->label ?? '',
                    'outside_year' => $outsideYear,
                    'my_classes'   => $myClassDates[$key] ?? [],
                ];
            }

            // Pad into 7-column week rows
            $padded = array_merge(array_fill(0, $offset, null), $dayCells);
            $rem    = count($padded) % 7;
            if ($rem > 0) {
                $padded = array_merge($padded, array_fill(0, 7 - $rem, null));
            }

            $months[] = [
                'name'  => $first->format('F Y'),
                'weeks' => array_chunk($padded, 7),
            ];

            $monthCursor->addMonth();
        }

        // Off days within this year's range (future ones) for the summary list
        $upcomingOffDays = $offDays
            ->filter(fn($d) => $d->date->gte($today))
            ->sortBy(fn($d) => $d->date->toDateString())
            ->map(function ($offDay) use ($myClassDates) {
                $key = $offDay->date->toDateString();
                return [
                    'date'    => $offDay->date,
                    'label'   => $offDay->label ?: 'No class',
                    'classes' => $myClassDates[$key] ?? [],
                ];
            })
            ->values();

        return view('handler.calendar', compact(
            'months', 'upcomingOffDays',
            'schoolYear', 'schoolStart', 'schoolEnd',
            'prevYear', 'nextYear'
        ));
    }
}
