<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingRatingScale extends Model
{
    protected $fillable = [
        'grading_exercise_id', 'label', 'description', 'marks_deducted', 'is_automatic_fail', 'sort_order',
    ];

    protected $casts = [
        'is_automatic_fail' => 'boolean',
        'marks_deducted'    => 'decimal:2',
    ];

    public function exercise()
    {
        return $this->belongsTo(GradingExercise::class, 'grading_exercise_id');
    }
}
