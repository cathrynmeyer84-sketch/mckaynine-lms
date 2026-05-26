<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\InstructorFeeStatement;
use App\Services\FeePeriodCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeStatementController extends Controller
{
    public function release(Request $request)
    {
        $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'term'          => 'required|string',
        ]);

        $calculator = new FeePeriodCalculator();
        $term       = $request->term;

        // Detect whether this is a term key ('2026-T2') or a month key ('2026-04')
        if (preg_match('/^\d{4}-T[1-4]$/', $term)) {
            [$start, $end] = $calculator->termPeriodDates($term);
            $label = collect($calculator->buildTermOptions())
                ->firstWhere('value', $term)['label'] ?? $term;
        } else {
            // Monthly: '2026-04'
            $start = Carbon::parse($term . '-01')->startOfDay();
            $end   = $start->copy()->endOfMonth()->endOfDay();
            $label = $start->format('F Y');
        }

        $instructor = Instructor::findOrFail($request->instructor_id);
        $results    = $calculator->calculate($start, $end, collect([$instructor]));
        $row        = $results[$instructor->id];

        $lines = collect($row['lines'])->map(fn($l) => [
            'class_id'   => $l['class']?->id,
            'class_name' => $l['class']?->name ?? 'Private Lessons',
            'type'       => $l['type'],
            'detail'     => $l['detail'],
            'fee'        => round($l['fee'], 2),
        ])->all();

        InstructorFeeStatement::updateOrCreate(
            ['instructor_id' => $instructor->id, 'term' => $request->term],
            [
                'period_label' => $label,
                'period_start' => $start->toDateString(),
                'period_end'   => $end->toDateString(),
                'lines'        => $lines,
                'total'        => round($row['total'], 2),
                'is_released'  => true,
                'released_at'  => now(),
            ]
        );

        return back()->with('success', 'Fee statement released to instructor.');
    }

    public function pay(Request $request, InstructorFeeStatement $statement)
    {
        $request->validate([
            'payment_notes' => 'nullable|string',
        ]);

        $statement->update([
            'is_paid'       => true,
            'paid_at'       => now(),
            'payment_notes' => $request->payment_notes,
        ]);

        return back()->with('success', 'Marked as paid.');
    }

    public function unpay(InstructorFeeStatement $statement)
    {
        $statement->update([
            'is_paid'  => false,
            'paid_at'  => null,
        ]);

        return back()->with('success', 'Payment mark removed.');
    }
}
