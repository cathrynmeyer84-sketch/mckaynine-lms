<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentSpecialDate extends Model
{
    protected $fillable = ['date', 'start_time', 'max_bookings', 'notes'];

    protected $casts = ['date' => 'date'];
}
