<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSlot extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'start_time', 'end_time', 'max_bookings', 'is_available', 'notes'];

    protected $casts = ['date' => 'date', 'is_available' => 'boolean'];

    public function assessmentRequests() { return $this->hasMany(AssessmentRequest::class); }

    public function getBookedCountAttribute(): int { return $this->assessmentRequests()->count(); }
    public function hasAvailability(): bool { return $this->is_available && $this->booked_count < $this->max_bookings; }
}
