<?php

namespace App\Services;

use App\Models\ClassDate;
use App\Models\ClassType;
use App\Models\DogClass;
use App\Models\Enrolment;
use App\Models\PrivateLesson;
use Carbon\Carbon;

class FeePeriodCalculator
{
    /**
     * Calculate period fees for a collection of instructors.
     */
    public function calculate(Carbon $periodStart, Carbon $periodEnd, $instructors): array
    {
        // --- Pre-load all term class dates that fall in this period ---
        $termClassDates = ClassDate::where('is_off_week', false)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->whereHas('dogClass.classType', fn($q) => $q->where('duration_type', 'term'))
            ->with(['dogClass.classType', 'dogClass.confirmedEnrolments.dog', 'registers'])
            ->get();

        // Pre-compute term fee structure per class (avoid recalculating per instructor)
        $termFeeStructures = [];
        foreach ($termClassDates->groupBy('class_id') as $classId => $dates) {
            $termFeeStructures[$classId] = $this->buildTermFeeStructure($dates->first()->dogClass);
        }

        // --- Build the three calendar months in this period ---
        $months = [];
        $cursor = $periodStart->copy()->startOfMonth();
        while ($cursor->lte($periodEnd)) {
            $months[] = [$cursor->copy()->startOfMonth(), $cursor->copy()->endOfMonth()];
            $cursor->addMonth();
        }

        $results = [];

        foreach ($instructors as $instructor) {
            $lines = [];
            $total = 0;

            // --- Term classes ---
            foreach ($termClassDates->groupBy('class_id') as $classId => $dates) {
                $structure   = $termFeeStructures[$classId];
                $weeklyRate  = $structure['weekly_rate'];
                $dogClass    = $dates->first()->dogClass;
                $enrolled    = $dogClass->confirmedEnrolments;

                $sessions      = 0;
                $dogSessions   = 0;
                $classFee      = 0;
                $dogSessionMap = []; // enrolment_id => per-dog attendance

                foreach ($dates as $classDate) {
                    // Stand-in overrides ALL dogs for this session
                    $effectiveId = $classDate->stand_in_instructor_id;
                    $sessionDogs = 0;

                    foreach ($enrolled as $enrolment) {
                        $instrId = $effectiveId ?? $enrolment->assigned_instructor_id;
                        if ($instrId != $instructor->id) continue;

                        $reg = $classDate->registers->firstWhere('enrolment_id', $enrolment->id);
                        $att = $reg?->attendance;
                        if (!$att || $att === 'present') {
                            $sessionDogs++;
                            $eid = $enrolment->id;
                            if (!isset($dogSessionMap[$eid])) {
                                $dogSessionMap[$eid] = [
                                    'name'     => $enrolment->dog?->name ?? '—',
                                    'discount' => (bool) $enrolment->dog?->multi_dog_discount,
                                    'attended' => 0,
                                    'fee'      => 0,
                                ];
                            }
                            $dogSessionMap[$eid]['attended']++;
                            $dogSessionMap[$eid]['fee'] += $weeklyRate * 0.40;
                        }
                    }

                    if ($sessionDogs > 0) {
                        $sessions++;
                        $dogSessions += $sessionDogs;
                        $classFee    += $weeklyRate * 0.40 * $sessionDogs;
                    }
                }

                if ($classFee > 0) {
                    $totalDates = count($dates);
                    $dogs = array_values(array_map(
                        fn($d) => array_merge($d, ['total' => $totalDates, 'fee' => round($d['fee'], 2)]),
                        $dogSessionMap
                    ));
                    $lines[] = [
                        'class'  => $dogClass,
                        'type'   => 'term',
                        'detail' => $sessions . ' session' . ($sessions !== 1 ? 's' : '')
                                  . ' · ' . $dogSessions . ' dog-session' . ($dogSessions !== 1 ? 's' : ''),
                        'fee'    => $classFee,
                        'dogs'   => $dogs,
                    ];
                    $total += $classFee;
                }
            }

            // --- Ongoing / monthly classes (aggregated per class across the 3 months) ---
            $ongoingByClass = [];

            foreach ($months as [$monthStart, $monthEnd]) {
                $ongoingEnrolments = Enrolment::where('assigned_instructor_id', $instructor->id)
                    ->whereIn('status', ['confirmed'])
                    ->whereHas('dogClass', function ($q) use ($monthStart, $monthEnd) {
                        $q->whereHas('classType', fn($q2) => $q2->where('duration_type', 'ongoing'))
                          ->where('start_date', '<=', $monthEnd)
                          ->where('end_date', '>=', $monthStart);
                    })
                    ->with(['dogClass.classType', 'dog'])
                    ->get();

                foreach ($ongoingEnrolments->groupBy('class_id') as $classId => $classEnrolments) {
                    $dogClass  = $classEnrolments->first()->dogClass;
                    $basePrice = (float) ($dogClass->classType?->monthly_fee_per_dog ?? $dogClass->course_price ?? 0);
                    $monthFee  = 0;

                    if (!isset($ongoingByClass[$classId])) {
                        $ongoingByClass[$classId] = [
                            'class'         => $dogClass,
                            'active_months' => 0,
                            'dog_months'    => 0,
                            'fee'           => 0,
                            'dog_data'      => [],
                        ];
                    }

                    foreach ($classEnrolments as $enrolment) {
                        $factor   = $enrolment->dog?->multi_dog_discount ? 0.75 : 1.0;
                        $dogFee   = $basePrice * $factor * 0.40;
                        $monthFee += $dogFee;

                        $dogId = $enrolment->dog_id;
                        if (!isset($ongoingByClass[$classId]['dog_data'][$dogId])) {
                            $ongoingByClass[$classId]['dog_data'][$dogId] = [
                                'name'     => $enrolment->dog?->name ?? '—',
                                'discount' => (bool) $enrolment->dog?->multi_dog_discount,
                                'months'   => 0,
                                'fee'      => 0,
                            ];
                        }
                        $ongoingByClass[$classId]['dog_data'][$dogId]['months']++;
                        $ongoingByClass[$classId]['dog_data'][$dogId]['fee'] += $dogFee;
                    }

                    $ongoingByClass[$classId]['active_months']++;
                    $ongoingByClass[$classId]['dog_months'] += $classEnrolments->count();
                    $ongoingByClass[$classId]['fee']        += $monthFee;
                }
            }

            foreach ($ongoingByClass as $data) {
                if ($data['fee'] > 0) {
                    $dm   = $data['dog_months'];
                    $mo   = $data['active_months'];
                    $dogs = array_values(array_map(
                        fn($d) => array_merge($d, ['fee' => round($d['fee'], 2)]),
                        $data['dog_data']
                    ));
                    $lines[] = [
                        'class'  => $data['class'],
                        'type'   => 'ongoing',
                        'detail' => $mo . ' month' . ($mo !== 1 ? 's' : '')
                                  . ' · ' . $dm . ' dog-month' . ($dm !== 1 ? 's' : ''),
                        'fee'    => $data['fee'],
                        'dogs'   => $dogs,
                    ];
                    $total += $data['fee'];
                }
            }

            // --- Private lessons ---
            $lessons = PrivateLesson::where('instructor_id', $instructor->id)
                ->where('status', 'completed')
                ->whereNotNull('confirmed_date')
                ->whereBetween('confirmed_date', [$periodStart, $periodEnd])
                ->get();

            if ($lessons->count() > 0) {
                $lessonFee = $lessons->sum(fn($l) => (float) ($l->fee ?? 0)) * 0.40;
                $lines[] = [
                    'class'  => null,
                    'type'   => 'private',
                    'detail' => $lessons->count() . ' lesson' . ($lessons->count() !== 1 ? 's' : ''),
                    'fee'    => $lessonFee,
                ];
                $total += $lessonFee;
            }

            $results[$instructor->id] = [
                'instructor' => $instructor,
                'lines'      => $lines,
                'total'      => $total,
            ];
        }

        return $results;
    }

    /**
     * Build term fee structure for a class.
     */
    public function buildTermFeeStructure(DogClass $dogClass): array
    {
        // Use the already-eager-loaded collection if available; otherwise query fresh
        $enrolled  = $dogClass->relationLoaded('confirmedEnrolments')
            ? $dogClass->confirmedEnrolments
            : $dogClass->confirmedEnrolments()->with('dog')->get();

        $termWeeks = (int) ($dogClass->classType?->term_weeks ?? 1);
        $price     = (float) ($dogClass->classType?->course_price ?? $dogClass->course_price ?? 0);

        $sumPrice = 0;
        foreach ($enrolled as $enrolment) {
            $factor    = $enrolment->dog?->multi_dog_discount ? 0.75 : 1.0;
            $sumPrice += $price * $factor;
        }
        $count    = max(1, $enrolled->count());
        $avgPrice = $sumPrice / $count;
        $weekly   = $avgPrice / max(1, $termWeeks);

        return [
            'enrolled_count' => $enrolled->count(),
            'avg_price'      => $avgPrice,
            'term_weeks'     => $termWeeks,
            'weekly_rate'    => $weekly,
        ];
    }

    /**
     * Build dropdown options: 1 term ahead → 8 terms back (10 total).
     */
    public function buildTermOptions(): array
    {
        $options = [];
        $now     = now();
        $year    = $now->year;
        $q       = (int) ceil($now->month / 3);

        // Start one term ahead
        $q++;
        if ($q > 4) { $q = 1; $year++; }

        for ($i = 0; $i < 10; $i++) {
            $startMonth = ($q - 1) * 3 + 1;
            $start = Carbon::create($year, $startMonth, 1);
            $end   = $start->copy()->addMonths(3)->subDay();

            $options[] = [
                'value' => $year . '-T' . $q,
                'label' => 'Term ' . $q . ' ' . $year
                         . ' (' . $start->format('M') . ' – ' . $end->format('M') . ')',
            ];

            $q--;
            if ($q < 1) { $q = 4; $year--; }
        }

        return $options;
    }

    public function currentTermValue(): string
    {
        $now = now();
        return $now->year . '-T' . (int) ceil($now->month / 3);
    }

    /**
     * '2026-T2' → [Carbon(Apr 1), Carbon(Jun 30 23:59:59)]
     */
    public function termPeriodDates(string $term): array
    {
        [$year, $t]  = explode('-T', $term);
        $q           = (int) $t;
        $startMonth  = ($q - 1) * 3 + 1;
        $start       = Carbon::create((int) $year, $startMonth, 1)->startOfDay();
        $end         = $start->copy()->addMonths(3)->subSecond();
        return [$start, $end];
    }
}
