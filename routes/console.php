<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('assessments:send-reminders')->dailyAt('08:00');
Schedule::command('private-lessons:send-reminders')->dailyAt('08:00');
Schedule::command('calendar:send-off-day-reminders')->dailyAt('08:00');
Schedule::command('classes:send-content-notifications')->everyFifteenMinutes();
Schedule::command('billing:generate-monthly-invoices')->monthlyOn(1, '06:00');
