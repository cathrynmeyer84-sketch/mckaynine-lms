<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CalendarDay extends Model
{
    protected $fillable = ['date', 'is_active', 'label', 'reminder_sent'];

    protected $casts = [
        'date'          => 'date:Y-m-d',
        'is_active'     => 'boolean',
        'reminder_sent' => 'boolean',
    ];

    // Returns true if the day is active (no record = active by default)
    public static function isDayActive(Carbon $date): bool
    {
        $record = self::whereDate('date', $date->toDateString())->orderByDesc('id')->first();
        return $record ? $record->is_active : true;
    }

    public static function getLabel(Carbon $date): ?string
    {
        return self::whereDate('date', $date->toDateString())->orderByDesc('id')->value('label');
    }
}
