<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_request_id', 'evaluator_id',
        'step1_score', 'step1_notes',
        'step2_score', 'step2_notes',
        'step3_score', 'step3_notes',
        'step4_score', 'step4_notes',
        'step5_score', 'step5_notes',
        'step6_score', 'step6_notes',
        'step7_score', 'step7_notes',
        'step7_skipped', 'step7_skip_reason',
        'recommended_outcome', 'final_outcome', 'override_reason',
        'global_notes', 'recommended_class_name', 'recommended_class_url',
        'recommended_class_ids',
        'status', 'submitted_at',
    ];

    protected $casts = [
        'step7_skipped'          => 'boolean',
        'submitted_at'           => 'datetime',
        'recommended_class_ids'  => 'array',
    ];

    public function assessmentRequest() { return $this->belongsTo(AssessmentRequest::class); }
    public function evaluator() { return $this->belongsTo(User::class, 'evaluator_id'); }

    public function getScores(): array
    {
        $scores = [];
        for ($i = 1; $i <= 7; $i++) {
            $score = $this->{"step{$i}_score"};
            if ($score !== null) $scores[] = $score;
        }
        return $scores;
    }

    public function calculateRecommendedOutcome(): string
    {
        $scores = $this->getScores();
        if (empty($scores)) return 'group_class';
        $avg = array_sum($scores) / count($scores);
        if ($avg <= 2.5) return 'group_class';
        if ($avg <= 3.5) return 'private_lessons';
        return 'behaviourist';
    }
}
