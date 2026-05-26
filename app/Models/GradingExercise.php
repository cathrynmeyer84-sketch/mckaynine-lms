<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingExercise extends Model
{
    protected $fillable = [
        'class_type_id', 'type', 'name', 'description', 'starting_marks', 'max_marks',
        'target_time_seconds', 'allow_second_attempt', 'sort_order',
    ];

    protected $casts = [
        'starting_marks'      => 'decimal:2',
        'max_marks'           => 'decimal:2',
        'allow_second_attempt' => 'boolean',
    ];

    public function classType()
    {
        return $this->belongsTo(ClassType::class);
    }

    public function deductionEvents()
    {
        return $this->hasMany(GradingDeductionEvent::class)->orderBy('sort_order');
    }

    public function ratingScales()
    {
        return $this->hasMany(GradingRatingScale::class)->orderBy('sort_order');
    }
}
