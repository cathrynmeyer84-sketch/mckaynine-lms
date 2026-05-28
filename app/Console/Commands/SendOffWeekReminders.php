<?php

namespace App\Console\Commands;

use App\Models\{AppSetting, CalendarDay, ClassDate};
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendOffWeekReminders extends Command
{
    protected $signature   = 'calendar:send-off-day-reminders';
    protected $description = 'Send in-app reminder messages to handlers whose class is cancelled on an upcoming off day';

    public function handle(MessageService $messages): void
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

        $offDate   = $offDay->date->format('l, d F Y');
        $offReason = $offDay->label ?: 'a scheduled break';

        // Find class dates that fall on this specific off day
        $classDates = ClassDate::where('date', $targetDate)
            ->where('is_off_week', true)
            ->with(['class.enrolments' => fn($q) => $q->where('status', 'confirmed')->with(['handler.user', 'dog'])])
            ->get();

        $sent = 0;
        foreach ($classDates as $classDate) {
            $class = $classDate->class;

            // Find the next active class date after this off day
            $nextActive = ClassDate::where('class_id', $class->id)
                ->where('date', '>', $classDate->date->toDateString())
                ->where('is_off_week', false)
                ->orderBy('date')
                ->first();
            $nextClassDate = $nextActive ? $nextActive->date->format('l, d F Y') : 'TBC';

            foreach ($class->enrolments as $enrolment) {
                $handlerUser = $enrolment->handler?->user;
                if (!$handlerUser) continue;

                $messages->sendTemplateToHandler(
                    'off_week_reminder',
                    $handlerUser,
                    [
                        'handler'         => $enrolment->handler,
                        'dog'             => $enrolment->dog,
                        'class'           => $class,
                        'off_date'        => $offDate,
                        'off_reason'      => $offReason,
                        'next_class_date' => $nextClassDate,
                    ],
                    classId: $class->id,
                );
                $sent++;
            }
        }

        $offDay->update(['reminder_sent' => true]);

        $this->info("Sent {$sent} off-day reminder message(s) for {$targetDate}.");
    }
}
