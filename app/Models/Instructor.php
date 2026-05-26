<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'first_name', 'last_name', 'email', 'phone', 'bio', 'is_active',
        'private_lessons_enabled', 'private_lesson_bio', 'profile_photo_path', 'birthday', 'payment_frequency'];

    protected $casts = [
        'is_active'               => 'boolean',
        'private_lessons_enabled' => 'boolean',
        'birthday'                => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function classes() { return $this->belongsToMany(DogClass::class, 'class_instructor', 'instructor_id', 'class_id')->withPivot('is_lead')->withTimestamps(); }
    public function goals() { return $this->hasMany(HandlerGoal::class); }
    public function privateLessonAvailabilities() { return $this->hasMany(PrivateLessonAvailability::class); }
    public function privateLessonBlocks() { return $this->hasMany(PrivateLessonBlock::class); }
    public function privateLessons() { return $this->hasMany(PrivateLesson::class); }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
