<?php

namespace App\Http\Controllers;

use App\Models\AssessmentRequest;
use App\Models\AssessmentScore;
use Illuminate\Http\Request;

class AssessmentScoreController extends Controller
{
    public function form(AssessmentRequest $assessmentRequest)
    {
        $assessmentRequest->load(['handler', 'dog', 'scores']);
        $existing = $assessmentRequest->scores;
        return view('instructor.assessment-score', compact('assessmentRequest', 'existing'));
    }

    public function store(Request $request, AssessmentRequest $assessmentRequest)
    {
        $data = $request->validate([
            'step1_score' => 'required|integer|min:1|max:5',
            'step2_score' => 'required|integer|min:1|max:5',
            'step3_score' => 'required|integer|min:1|max:5',
            'step4_score' => 'required|integer|min:1|max:5',
            'step5_score' => 'required|integer|min:1|max:5',
            'step6_score' => 'required|integer|min:1|max:5',
            'step7_skipped' => 'boolean',
            'step7_score' => 'nullable|integer|min:1|max:5',
            'step7_skip_reason' => 'nullable|string',
            'global_notes' => 'nullable|string',
            'staff_notes' => 'nullable|string',
            'final_outcome' => 'nullable|in:group_class,private_lessons,behaviourist',
            'override_reason' => 'nullable|string',
        ]);

        $score = AssessmentScore::updateOrCreate(
            ['assessment_request_id' => $assessmentRequest->id],
            array_merge($data, [
                'evaluator_id' => auth()->id(),
                'submitted_at' => now(),
                'status' => 'submitted',
            ])
        );

        $score->recommended_outcome = $score->calculateRecommendedOutcome();
        if (empty($data['final_outcome'])) {
            $score->final_outcome = $score->recommended_outcome;
        }
        $score->save();

        $assessmentRequest->update(['status' => 'scored']);

        return redirect()->route('admin.assessments.show', $assessmentRequest)
            ->with('success', 'Assessment scored successfully.');
    }
}
