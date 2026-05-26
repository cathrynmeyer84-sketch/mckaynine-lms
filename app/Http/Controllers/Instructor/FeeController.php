<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ClassDate;
use App\Models\DogClass;
use App\Models\Enrolment;
use App\Models\InstructorFeeStatement;
use App\Services\FeePeriodCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor) abort(403);

        $statements = InstructorFeeStatement::where('instructor_id', $instructor->id)
            ->where('is_released', true)
            ->orderBy('period_start', 'desc')
            ->get();

        return view('instructor.fees.index', compact('statements'));
    }

    public function show(InstructorFeeStatement $statement)
    {
        $instructor = auth()->user()->instructor;
        if (!$instructor || $statement->instructor_id !== $instructor->id || !$statement->is_released) {
            abort(403);
        }

        $periodStart = Carbon::parse($statement->period_start)->startOfDay();
        $periodEnd   = Carbon::parse($statement->period_end)->endOfDay();

        $dogBreakdowns = [];

        foreach ($statement->lines as $line) {
            $classId = $line['class_id'] ?? null;
            if (!$classId) continue;

            if ($line['type'] === 'term') {
                $dogClass = DogClass::with(['classType', 'confirmedEnrolments.dog'])->find($classId);
                if (!$dogClass) continue;

                $classDates = ClassDate::where('class_id', $classId)
                    ->where('is_off_week', false)
                    ->whereBetween('date', [$periodStart, $periodEnd])
                    ->with('registers')
                    ->orderBy('date')
                    ->get();

                $structure  = (new FeePeriodCalculator())->buildTermFeeStructure($dogClass);
                $weeklyRate = $structure['weekly_rate'];
                $totalSessions = $classDates->count();
                $dogs = [];

                foreach ($dogClass->confirmedEnrolments as $enrolment) {
                    $attended = 0;
                    foreach ($classDates as $classDate) {
                        $effectiveId = $classDate->stand_in_instructor_id ?? $enrolment->assigned_instructor_id;
                        if ($effectiveId != $instructor->id) continue;

                        $reg = $classDate->registers->firstWhere('enrolment_id', $enrolment->id);
                        $att = $reg?->attendance;
                        if (!$att || $att === 'present') {
                            $attended++;
                        }
                    }

                    if ($attended > 0) {
                        $dogs[] = [
                            'name'     => $enrolment->dog?->name ?? '—',
                            'attended' => $attended,
                            'total'    => $totalSessions,
                            'discount' => (bool) $enrolment->dog?->multi_dog_discount,
                            'fee'      => round($weeklyRate * 0.40 * $attended, 2),
                        ];
                    }
                }

                $dogBreakdowns[$classId] = ['type' => 'term', 'dogs' => $dogs];

            } elseif ($line['type'] === 'ongoing') {
                $dogClass = DogClass::with('classType')->find($classId);
                if (!$dogClass) continue;

                $basePrice = (float) ($dogClass->classType?->monthly_fee_per_dog ?? $dogClass->course_price ?? 0);

                // Build months within the statement period
                $months = [];
                $cursor = $periodStart->copy()->startOfMonth();
                while ($cursor->lte($periodEnd)) {
                    $months[] = [$cursor->copy()->startOfMonth(), $cursor->copy()->endOfMonth()->endOfDay()];
                    $cursor->addMonth();
                }

                $dogData = [];
                foreach ($months as [$monthStart, $monthEnd]) {
                    $enrolments = Enrolment::where('class_id', $classId)
                        ->where('assigned_instructor_id', $instructor->id)
                        ->where('status', 'confirmed')
                        ->whereHas('dogClass', fn($q) =>
                            $q->where('start_date', '<=', $monthEnd)
                              ->where('end_date', '>=', $monthStart)
                        )
                        ->with('dog')
                        ->get();

                    foreach ($enrolments as $enrolment) {
                        $dogId = $enrolment->dog_id;
                        if (!isset($dogData[$dogId])) {
                            $factor = $enrolment->dog?->multi_dog_discount ? 0.75 : 1.0;
                            $dogData[$dogId] = [
                                'name'     => $enrolment->dog?->name ?? '—',
                                'months'   => 0,
                                'discount' => (bool) $enrolment->dog?->multi_dog_discount,
                                'fee'      => 0,
                                '_factor'  => $factor,
                            ];
                        }
                        $dogData[$dogId]['months']++;
                        $dogData[$dogId]['fee'] += $basePrice * $dogData[$dogId]['_factor'] * 0.40;
                    }
                }

                $dogs = array_values(array_map(function ($d) {
                    $d['fee'] = round($d['fee'], 2);
                    unset($d['_factor']);
                    return $d;
                }, $dogData));

                $dogBreakdowns[$classId] = ['type' => 'ongoing', 'dogs' => $dogs, 'base_price' => $basePrice];
            }
        }

        return view('instructor.fees.show', compact('statement', 'dogBreakdowns'));
    }
}
