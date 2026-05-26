<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorBriefingItem extends Model
{
    protected $fillable = [
        'class_type_week_id', 'exercise_name', 'description',
        'suggested_time', 'image_path', 'sort_order',
    ];

    public function classTypeWeek()
    {
        return $this->belongsTo(ClassTypeWeek::class);
    }
}
