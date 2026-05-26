<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateLesson extends Model
{
    protected $fillable = [
        'handler_id', 'dog_id', 'instructor_id',
        'requested_date', 'requested_start_time',
        'confirmed_date', 'confirmed_start_time',
        'status', 'handler_notes', 'instructor_notes',
        'reschedule_note', 'fee',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'confirmed_date' => 'date',
    ];

    public function handler()
    {
        return $this->belongsTo(Handler::class);
    }

    public function dog()
    {
        return $this->belongsTo(Dog::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'              => 'Pending',
            'confirmed'            => 'Confirmed',
            'completed'            => 'Completed',
            'cancelled'            => 'Cancelled',
            'reschedule_requested' => 'Reschedule Requested',
            default                => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'              => 'badge-pending',
            'confirmed'            => 'badge-confirmed',
            'completed'            => 'badge-completed',
            'cancelled'            => 'badge-cancelled',
            'reschedule_requested' => 'badge-reschedule',
            default                => 'badge-default',
        };
    }
}
