<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingDeductionEvent extends Model
{
    protected $fillable = ['grading_exercise_id', 'event_name', 'marks_deducted', 'sort_order'];

    public function exercise()
    {
        return $this->belongsTo(GradingExercise::class, 'grading_exercise_id');
    }
}
