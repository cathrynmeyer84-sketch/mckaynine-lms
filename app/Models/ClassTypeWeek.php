<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTypeWeek extends Model
{
    protected $fillable = [
        'class_type_id', 'week_number', 'title', 'description',
        'youtube_url', 'practice_checklist', 'what_to_bring_next_week', 'extra_notes',
    ];

    public function classType()
    {
        return $this->belongsTo(ClassType::class);
    }

    public function briefingItems()
    {
        return $this->hasMany(InstructorBriefingItem::class)->orderBy('sort_order')->orderBy('id');
    }
}
