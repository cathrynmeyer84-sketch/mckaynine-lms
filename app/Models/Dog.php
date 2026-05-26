<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\ClassType;

class Dog extends Model
{
    use HasFactory;

    protected $fillable = [
        'handler_id', 'name', 'date_of_birth', 'breed', 'microchip_number', 'gender', 'spay_neuter_status',
        'gender_repro_status', 'origin_story', 'age_when_acquired', 'animal_buddies_at_home',
        'young_children_at_home', 'socialisation_other_dogs', 'socialisation_other_animals',
        'socialisation_people', 'socialisation_sights_sounds', 'socialisation_details',
        'training_ambition', 'training_goal', 'has_behaviour_problems', 'behaviour_problems_details',
        'has_health_issues', 'health_issues', 'photo_path', 'vaccination_card_path',
        'vaccination_expiry_date', 'is_retired', 'multi_dog_discount',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'vaccination_expiry_date' => 'date',
        'animal_buddies_at_home' => 'array',
        'young_children_at_home' => 'array',
        'has_behaviour_problems' => 'boolean',
        'has_health_issues' => 'boolean',
        'is_retired' => 'boolean',
        'multi_dog_discount' => 'boolean',
    ];

    public function handler() { return $this->belongsTo(Handler::class); }
    public function enrolments() { return $this->hasMany(Enrolment::class); }
    public function assessmentRequests() { return $this->hasMany(AssessmentRequest::class); }

    public function eligibleFor(ClassType $classType): bool
    {
        $prereqs = $classType->prerequisite_class_type_ids ?? [];
        if (empty($prereqs)) return true;

        $completedClassTypeIds = $this->enrolments()
            ->where('status', 'completed')
            ->join('classes', 'enrolments.class_id', '=', 'classes.id')
            ->pluck('classes.class_type_id')
            ->unique()
            ->all();

        // Any one prerequisite satisfied is sufficient (OR, not AND)
        foreach ($prereqs as $id) {
            if (in_array($id, $completedClassTypeIds)) return true;
        }

        return false;
    }

    public function missingPrerequisitesFor(ClassType $classType): \Illuminate\Support\Collection
    {
        $prereqs = $classType->prerequisite_class_type_ids ?? [];
        if (empty($prereqs)) return collect();

        $completedClassTypeIds = $this->enrolments()
            ->where('status', 'completed')
            ->join('classes', 'enrolments.class_id', '=', 'classes.id')
            ->pluck('classes.class_type_id')
            ->unique()
            ->all();

        // If any prerequisite is already met, nothing is "missing"
        foreach ($prereqs as $id) {
            if (in_array($id, $completedClassTypeIds)) return collect();
        }

        // None met — return all options so the handler knows what they can do
        return ClassType::whereIn('id', $prereqs)->get()->values();
    }

    public function getAgeAttribute(): ?string
    {
        if (!$this->date_of_birth) return null;
        return $this->date_of_birth->diffForHumans(null, true);
    }

    public function getAgeInMonthsAttribute(): ?int
    {
        if (!$this->date_of_birth) return null;
        return (int) $this->date_of_birth->diffInMonths(Carbon::now());
    }

    public function isEligibleForPuppyClass(?Carbon $startDate = null): bool
    {
        if (!$this->date_of_birth) return false;
        $checkDate = $startDate ?? Carbon::now();
        return $this->date_of_birth->diffInMonths($checkDate) < 4;
    }

    public function vaccinationExpiringSoon(): bool
    {
        return $this->vaccination_expiry_date && $this->vaccination_expiry_date->diffInDays(now()) <= 30;
    }
}
