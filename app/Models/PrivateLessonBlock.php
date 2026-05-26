<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateLessonBlock extends Model
{
    protected $fillable = ['instructor_id', 'blocked_date', 'reason'];

    protected $casts = [
        'blocked_date' => 'date',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
