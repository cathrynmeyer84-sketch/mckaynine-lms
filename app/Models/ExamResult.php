<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrolment_id', 'graded_by', 'exam_type', 'total_score', 'exercise_scores',
        'achievement_level', 'has_blocking_fault', 'instructor_comments',
        'evaluator_name', 'exam_date', 'status', 'submitted_at', 'released_at',
    ];

    protected $casts = [
        'has_blocking_fault' => 'boolean',
        'exercise_scores'    => 'array',
        'exam_date' => 'date',
        'submitted_at' => 'datetime',
        'released_at' => 'datetime',
        'total_score' => 'float',
    ];

    public function getIsReleasedAttribute(): bool
    {
        return $this->status === 'released';
    }

    public function enrolment() { return $this->belongsTo(Enrolment::class); }
    public function gradedBy() { return $this->belongsTo(User::class, 'graded_by'); }
    public function cgcBronzeGrade() { return $this->hasOne(CgcBronzeGrade::class); }
    public function eoGrade() { return $this->hasOne(EoGrade::class); }
}
