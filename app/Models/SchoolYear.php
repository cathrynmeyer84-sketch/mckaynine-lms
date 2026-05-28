<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = ['label', 'start_date', 'end_date'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * The school year that contains today.
     * If today falls between years, return the next upcoming one.
     * If all years are past, return the most recent one.
     */
    public static function current(): ?self
    {
        $today = Carbon::today();

        // Active: today falls within start–end
        $active = static::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->orderBy('start_date')
            ->first();

        if ($active) return $active;

        // Upcoming: next year that hasn't started yet
        $upcoming = static::where('start_date', '>', $today)
            ->orderBy('start_date')
            ->first();

        if ($upcoming) return $upcoming;

        // Fallback: most recently ended year
        return static::orderByDesc('end_date')->first();
    }
}
