<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id', 'date', 'start_time', 'end_time',
        'is_off_week', 'off_week_reason', 'week_number',
        'class_type_week_id', 'content_send_date', 'content_sent_at',
        'stand_in_instructor_id',
    ];

    protected $casts = [
        'date'              => 'date',
        'content_send_date' => 'datetime',
        'content_sent_at'   => 'datetime',
        'is_off_week'       => 'boolean',
    ];

    public function dogClass() { return $this->belongsTo(DogClass::class, 'class_id'); }
    public function registers() { return $this->hasMany(Register::class); }
    public function weeklyContent() { return $this->hasOne(WeeklyContent::class); }
    public function classTypeWeek() { return $this->belongsTo(ClassTypeWeek::class); }
    public function standInInstructor() { return $this->belongsTo(\App\Models\Instructor::class, 'stand_in_instructor_id'); }

    public function isContentPublished(): bool
    {
        return $this->weeklyContent &&
            $this->weeklyContent->is_published &&
            ($this->weeklyContent->publish_at === null || $this->weeklyContent->publish_at->isPast());
    }
}
