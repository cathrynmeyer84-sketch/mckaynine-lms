<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorFeeStatement extends Model
{
    protected $fillable = [
        'instructor_id',
        'term',
        'period_label',
        'period_start',
        'period_end',
        'lines',
        'total',
        'is_released',
        'released_at',
        'is_paid',
        'paid_at',
        'payment_notes',
    ];

    protected $casts = [
        'lines'        => 'array',
        'is_released'  => 'boolean',
        'is_paid'      => 'boolean',
        'period_start' => 'date',
        'period_end'   => 'date',
        'released_at'  => 'datetime',
        'paid_at'      => 'datetime',
        'total'        => 'decimal:2',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
