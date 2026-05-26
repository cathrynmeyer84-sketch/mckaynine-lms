<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateLessonAvailability extends Model
{
    protected $fillable = ['instructor_id', 'day_of_week', 'start_time'];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
