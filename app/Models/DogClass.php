<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DogClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name', 'class_type_id', 'has_final_exam', 'max_capacity',
        'start_date', 'end_date', 'start_time', 'end_time', 'location', 'description',
        'info_page_enabled', 'info_slug', 'show_enrol_button', 'enrolment_form_type',
        'course_price', 'enrolment_fee', 'course_fee_notes', 'fees_image_path', 'fees_image_mobile_path', 'info_address',
        'bank_name', 'bank_account_name', 'bank_account_number', 'bank_branch_code', 'bank_reference_note',
        'info_helps_with', 'info_what_to_bring', 'info_age_requirements', 'info_joining_notes',
        'info_hero_image_path', 'info_hero_image_mobile_path', 'testimonial_text', 'testimonial_name', 'testimonial_photo_path',
        'info_tagline', 'contact_phone', 'contact_email', 'next_class_ids', 'next_class_type_ids',
    ];

    protected $casts = [
        'has_final_exam'     => 'boolean',
        'info_page_enabled'  => 'boolean',
        'show_enrol_button'  => 'boolean',
        'start_date'         => 'date',
        'end_date'           => 'date',
        'course_price'       => 'decimal:2',
        'enrolment_fee'      => 'decimal:2',
        'info_helps_with'    => 'array',
        'info_what_to_bring' => 'array',
        'course_fee_notes'   => 'array',
        'next_class_ids'      => 'array',
        'next_class_type_ids' => 'array',
    ];

    public function classType()
    {
        return $this->belongsTo(ClassType::class);
    }

    public function instructors()
    {
        return $this->belongsToMany(Instructor::class, 'class_instructor', 'class_id', 'instructor_id')
            ->withPivot('is_lead')->withTimestamps();
    }

    public function dates() { return $this->hasMany(ClassDate::class, 'class_id')->orderBy('date'); }
    public function classDates() { return $this->dates(); }
    public function scheduledDates() { return $this->dates()->where('is_off_week', false); }
    public function offWeeks() { return $this->dates()->where('is_off_week', true); }
    public function enrolments() { return $this->hasMany(Enrolment::class, 'class_id'); }
    public function confirmedEnrolments() { return $this->enrolments()->where('status', 'confirmed'); }
    public function weeklyContents() { return $this->hasManyThrough(WeeklyContent::class, ClassDate::class, 'class_id', 'class_date_id'); }

    public function getStatusAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) return 'draft';

        $today    = now()->startOfDay();
        $yearAgo  = now()->subYear()->startOfDay();

        if ($this->end_date->lt($yearAgo))  return 'archived';
        if ($this->end_date->lt($today))    return 'completed';
        if ($this->start_date->lte($today)) return 'active';
        return 'upcoming';
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->confirmedEnrolments()->count();
    }

    public function hasCapacity(): bool
    {
        return !$this->max_capacity || $this->enrolled_count < $this->max_capacity;
    }

}
