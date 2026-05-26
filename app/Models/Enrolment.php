<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dog_id', 'handler_id', 'class_id', 'assessment_request_id', 'status', 'pathway',
        'class_type_requested', 'branch', 'checklist_acknowledged',
        'enrolled_at', 'confirmed_at', 'admin_notes', 'invoice_sent', 'invoice_reference',
        'vet_clearance_path', 'vet_clearance_requested_at', 'rejection_reason',
        'assigned_instructor_id',
    ];

    protected $casts = [
        'enrolled_at'                => 'date',
        'confirmed_at'               => 'date',
        'invoice_sent'               => 'boolean',
        'checklist_acknowledged'     => 'boolean',
        'vet_clearance_requested_at' => 'datetime',
    ];

    // Extended status labels for display
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'                    => 'Pending Review',
            'vet_clearance_requested'    => 'Vet Clearance Requested',
            'vet_clearance_review'       => 'Vet Clearance Under Review',
            'pending_class_assignment'   => 'Pending Class Assignment',
            'confirmed'                  => 'Confirmed',
            'waitlisted'                 => 'Waitlisted',
            'completed'                  => 'Completed',
            'withdrawn'                  => 'Withdrawn',
            default                      => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'confirmed', 'completed'        => 'badge-confirmed',
            'pending', 'waitlisted'         => 'badge-pending',
            'vet_clearance_requested',
            'vet_clearance_review',
            'pending_class_assignment'      => 'badge-active',
            default                         => 'badge',
        };
    }

    public function dog() { return $this->belongsTo(Dog::class); }
    public function handler() { return $this->belongsTo(Handler::class); }
    public function dogClass() { return $this->belongsTo(DogClass::class, 'class_id'); }
    public function assessmentRequest() { return $this->belongsTo(\App\Models\AssessmentRequest::class); }
    public function assignedInstructor() { return $this->belongsTo(\App\Models\Instructor::class, 'assigned_instructor_id'); }
    public function registers() { return $this->hasMany(Register::class); }
    public function examResult() { return $this->hasOne(ExamResult::class); }
    public function goals() { return $this->hasMany(HandlerGoal::class); }
    public function survey() { return $this->hasOne(Survey::class); }
}
