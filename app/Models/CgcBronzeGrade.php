<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CgcBronzeGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_result_id',
        'test1_rating', 'test1_notes', 'test2_rating', 'test2_notes',
        'test3a_rating', 'test3a_notes', 'test3b_rating', 'test3b_notes',
        'test4_rating', 'test4_notes', 'test5_rating', 'test5_notes',
        'test6_rating', 'test6_notes', 'test7_rating', 'test7_notes',
        'test8_rating', 'test8_notes', 'test9_rating', 'test9_notes',
        'test10_rating', 'test10_notes', 'test11_rating', 'test11_notes',
        'test12_rating', 'test12_notes', 'test13_rating', 'test13_notes',
        'calculated_score', 'has_blocking_fault', 'global_comments',
    ];

    protected $casts = ['has_blocking_fault' => 'boolean', 'calculated_score' => 'float'];

    // Deduction values per test per rating
    public static array $deductions = [
        'test1'  => ['excellent' => 0, 'very_good' => -2,  'conditional' => -5,  'not_ready' => -10],
        'test2'  => ['excellent' => 0, 'very_good' => -2,  'conditional' => -5,  'not_ready' => -10],
        'test3a' => ['excellent' => 0, 'very_good' => -1,  'conditional' => -2,  'not_ready' => -2],
        'test3b' => ['excellent' => 0, 'very_good' => -1,  'conditional' => -2,  'not_ready' => -3],
        'test4'  => ['excellent' => 0, 'very_good' => -1,  'conditional' => -3,  'not_ready' => -5],
        'test5'  => ['excellent' => 0, 'very_good' => -2,  'conditional' => -4,  'not_ready' => -10],
        'test6'  => ['excellent' => 0, 'very_good' => -1,  'conditional' => -3,  'not_ready' => -5],
        'test7'  => ['excellent' => 0, 'very_good' => -1,  'conditional' => -4,  'not_ready' => -10],
        'test8'  => ['excellent' => 0, 'very_good' => -1,  'conditional' => -3,  'not_ready' => -10],
        'test9'  => ['excellent' => 0, 'very_good' => -1,  'conditional' => -2,  'not_ready' => -5],
        'test10' => ['excellent' => 0, 'very_good' => -2,  'conditional' => -4,  'not_ready' => -10],
        'test11' => ['excellent' => 0, 'very_good' => -2,  'conditional' => -4,  'not_ready' => -10],
        'test12' => ['excellent' => 0, 'very_good' => -2,  'conditional' => -3,  'not_ready' => -5],
        'test13' => ['excellent' => 0, 'very_good' => -1,  'conditional' => -3,  'not_ready' => -5],
    ];

    public static array $maxMarks = [
        'test1' => 10, 'test2' => 10, 'test3a' => 2, 'test3b' => 3,
        'test4' => 5, 'test5' => 10, 'test6' => 5, 'test7' => 10,
        'test8' => 10, 'test9' => 5, 'test10' => 10, 'test11' => 10,
        'test12' => 5, 'test13' => 5,
    ];

    public function examResult() { return $this->belongsTo(ExamResult::class); }

    public function calculateScore(): float
    {
        $total = 100;
        $hasBlocking = false;
        foreach (array_keys(self::$maxMarks) as $test) {
            $rating = $this->{"{$test}_rating"};
            if ($rating && isset(self::$deductions[$test][$rating])) {
                $total += self::$deductions[$test][$rating];
                if (in_array($rating, ['conditional', 'not_ready'])) {
                    $hasBlocking = true;
                }
            }
        }
        $this->has_blocking_fault = $hasBlocking;
        return max(0, $total);
    }

    public function getAchievementLevel(float $score): string
    {
        if ($this->has_blocking_fault) return 'not_ready';
        if ($score >= 90) return 'excellent_pass';
        if ($score >= 80) return 'pass';
        return 'not_ready';
    }
}
