<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CalendarWeek extends Model
{
    protected $fillable = ['week_start', 'is_active', 'label', 'reminder_sent'];

    protected $casts = [
        'week_start'     => 'date',
        'is_active'      => 'boolean',
        'reminder_sent'  => 'boolean',
    ];

    // The Monday that contains a given date
    public static function mondayOf(Carbon $date): Carbon
    {
        return $date->copy()->startOfWeek(Carbon::MONDAY);
    }

    // Ensure a week row exists for the given Monday, returning it
    public static function findOrCreateForWeek(Carbon $monday): self
    {
        return self::firstOrCreate(['week_start' => $monday->toDateString()], ['is_active' => true]);
    }

    public static function isWeekActive(Carbon $date): bool
    {
        $monday = self::mondayOf($date)->toDateString();
        $week   = self::where('week_start', $monday)->first();
        return $week ? $week->is_active : true; // default active if not in calendar
    }

    public function getWeekEndAttribute(): Carbon
    {
        return $this->week_start->copy()->endOfWeek(Carbon::SUNDAY);
    }
}
