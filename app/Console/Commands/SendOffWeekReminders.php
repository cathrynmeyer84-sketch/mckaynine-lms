<?php

namespace App\Console\Commands;

use App\Mail\OffWeekReminder;
use App\Models\{AppSetting, CalendarDay, ClassDate, Enrolment};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendOffWeekReminders extends Command
{
    protected $signature   = 'calendar:send-off-day-reminders';
    protected $description = 'Send reminder emails to handlers whose class is cancelled on an upcoming off day';

    public function handle(): void
    {
        $days = (int) AppSetting::get('off_day_reminder_days', '3');
        $targetDate = Carbon::now()->addDays($days)->toDateString();

        // Find off days that land on the target date and haven't had a reminder sent
        $offDay = CalendarDay::where('date', $targetDate)
            ->where('is_active', false)
            ->where('reminder_sent', false)
            ->first();

        if (!$offDay) {
            $this->info("No cancelled-day reminder needed for {$targetDate}.");
            return;
        }

        // Find class dates that fall on this specific off day
        $classDates = ClassDate::where('date', $targetDate)
            ->where('is_off_week', true)
            ->with(['class.enrolments' => fn($q) => $q->where('status', 'confirmed')->with(['handler.user', 'dog'])])
            ->get();

        $sent = 0;
        foreach ($classDates as $classDate) {
            foreach ($classDate->class->enrolments as $enrolment) {
                $email = $enrolment->handler?->user?->email;
                if (!$email) continue;

                Mail::to($email)->send(new OffWeekReminder($enrolment, $offDay, $classDate));
                $sent++;
            }
        }

        $offDay->update(['reminder_sent' => true]);

        $this->info("Sent {$sent} off-day reminder(s) for {$targetDate}.");
    }
}
