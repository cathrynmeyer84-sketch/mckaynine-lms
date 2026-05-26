<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\InstructorFeeStatement;
use App\Services\FeePeriodCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $calculator   = new FeePeriodCalculator();
        $termOptions  = $calculator->buildTermOptions();
        $currentTerm  = $calculator->currentTermValue();
        $selectedTerm = $request->get('term', $currentTerm);

        [$periodStart, $periodEnd] = $calculator->termPeriodDates($selectedTerm);

        $instructors = Instructor::where('is_active', true)->orderBy('first_name')->get();

        // --- Termly instructors: one calculation across the full period ---
        $termlyInstructors = $instructors->where('payment_frequency', '!=', 'monthly')->values();
        $termlyResults     = $calculator->calculate($periodStart, $periodEnd, $termlyInstructors);

        // --- Monthly instructors: one calculation per calendar month ---
        $monthlyInstructors = $instructors->where('payment_frequency', 'monthly')->values();

        // Build the 3 month windows in this term
        $monthKeys = [];
        $cursor    = $periodStart->copy()->startOfMonth();
        while ($cursor->lte($periodEnd)) {
            $monthKeys[] = $cursor->format('Y-m');
            $cursor->addMonth();
        }

        $monthlyResults = [];
        foreach ($monthlyInstructors as $instructor) {
            $monthlyResults[$instructor->id] = [
                'instructor' => $instructor,
                'months'     => [],
                'total'      => 0,
            ];
        }

        if ($monthlyInstructors->isNotEmpty()) {
            foreach ($monthKeys as $monthKey) {
                $mStart    = Carbon::parse($monthKey . '-01')->startOfDay();
                $mEnd      = $mStart->copy()->endOfMonth()->endOfDay();
                $monthCalc = $calculator->calculate($mStart, $mEnd, $monthlyInstructors);

                foreach ($monthCalc as $instrId => $row) {
                    $monthlyResults[$instrId]['months'][$monthKey] = [
                        'label' => Carbon::parse($monthKey . '-01')->format('F Y'),
                        'key'   => $monthKey,
                        'lines' => $row['lines'],
                        'total' => $row['total'],
                    ];
                    $monthlyResults[$instrId]['total'] += $row['total'];
                }
            }
        }

        // --- Load stored statements ---
        $statements = InstructorFeeStatement::where('term', $selectedTerm)
            ->whereIn('instructor_id', $termlyInstructors->pluck('id'))
            ->get()
            ->keyBy('instructor_id');

        $monthStatements = InstructorFeeStatement::whereIn('term', $monthKeys)
            ->whereIn('instructor_id', $monthlyInstructors->pluck('id'))
            ->get()
            ->groupBy('instructor_id')
            ->map(fn($s) => $s->keyBy('term'));

        $grandTotal = collect($termlyResults)->sum('total')
                    + collect($monthlyResults)->sum('total');

        return view('admin.fees.index', compact(
            'termOptions', 'selectedTerm', 'periodStart', 'periodEnd',
            'termlyResults', 'monthlyResults', 'monthKeys',
            'statements', 'monthStatements',
            'grandTotal'
        ));
    }
}
