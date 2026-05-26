<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EoGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id', 'course_level',
        'ex1_penalties', 'ex2_penalties', 'ex3_penalties', 'ex4_penalties',
        'ex5_penalties', 'ex6_penalties', 'ex7_penalties',
        'ex1_score', 'ex2_score', 'ex3_score', 'ex4_score',
        'ex5_score', 'ex6_score', 'ex7_score', 'total_score', 'global_comments',
    ];

    protected $casts = [
        'ex1_penalties' => 'array', 'ex2_penalties' => 'array', 'ex3_penalties' => 'array',
        'ex4_penalties' => 'array', 'ex5_penalties' => 'array', 'ex6_penalties' => 'array',
        'ex7_penalties' => 'array',
        'ex1_score' => 'float', 'ex2_score' => 'float', 'ex3_score' => 'float',
        'ex4_score' => 'float', 'ex5_score' => 'float', 'ex6_score' => 'float',
        'ex7_score' => 'float', 'total_score' => 'float',
    ];

    public static array $maxMarks = [1 => 5, 2 => 5, 3 => 30, 4 => 10, 5 => 15, 6 => 30, 7 => 5];

    public function examResult() { return $this->belongsTo(ExamResult::class); }

    public function getAchievementLevel(float $score): string
    {
        if ($score >= 91) return 'merit_pass';
        if ($score >= 80) return 'pass';
        if ($score >= 70) return 'review';
        return 'fail';
    }
}
