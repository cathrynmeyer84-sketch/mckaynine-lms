<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentAvailability extends Model
{
    protected $fillable = ['day_of_week', 'start_time', 'max_bookings', 'notes'];

    /** Human-readable day name */
    public function getDayNameAttribute(): string
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$this->day_of_week] ?? '';
    }
}
