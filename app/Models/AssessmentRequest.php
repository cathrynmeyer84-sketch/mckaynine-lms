<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'dog_id', 'handler_id', 'dog_age_description', 'gender_repro_status',
        'where_got_dog', 'age_acquired', 'how_long_had_dog',
        'health_concerns', 'training_goals', 'desired_outcomes', 'specific_issues',
        'response_to_new_people', 'behaviour_around_dogs', 'aggression_history',
        'aggression_targets', 'aggression_details', 'prior_training',
        'comfort_in_busy_environments', 'comfortable_with_assessment',
        'open_to_recommendation', 'additional_notes',
        'terms_agreed', 'requirements_acknowledged', 'status', 'assessment_slot_id',
    ];

    protected $casts = [
        'training_goals'           => 'array',
        'aggression_targets'       => 'array',
        'terms_agreed'             => 'boolean',
        'requirements_acknowledged'=> 'boolean',
        'response_to_new_people'   => 'integer',
    ];

    public function dog() { return $this->belongsTo(Dog::class); }
    public function handler() { return $this->belongsTo(Handler::class); }
    public function slot() { return $this->belongsTo(AssessmentSlot::class, 'assessment_slot_id'); }
    public function scores() { return $this->hasOne(AssessmentScore::class); }
}
