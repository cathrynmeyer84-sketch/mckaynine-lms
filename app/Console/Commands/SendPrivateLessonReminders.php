<?php

namespace App\Console\Commands;

use App\Models\PrivateLesson;
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPrivateLessonReminders extends Command
{
    protected $signature   = 'private-lessons:send-reminders';
    protected $description = 'Send 24-hour reminder messages to handlers and instructors for confirmed private lessons tomorrow';

    public function handle(): void
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $lessons = PrivateLesson::where('status', 'confirmed')
            ->whereDate('requested_date', $tomorrow)
            ->with(['handler.user', 'instructor.user', 'dog'])
            ->get();

        $sent = 0;

        foreach ($lessons as $lesson) {
            $dogName = $lesson->dog->name;
            $date    = $lesson->requested_date->format('d M Y');
            $time    = Carbon::parse($lesson->requested_start_time)->format('g:i A');

            // Send reminder to handler
            if ($lesson->handler?->user) {
                app(MessageService::class)->createDirect(
                    1,
                    $lesson->handler->user->id,
                    "Reminder: Private Lesson Tomorrow — {$dogName}",
                    [['type' => 'text', 'content' => "Just a reminder that {$dogName}'s private lesson is tomorrow, {$date} at {$time}. See you there!"]]
                );
                $this->info("Handler reminder sent to {$lesson->handler->user->email} for {$dogName}");
                $sent++;
            }

            // Send reminder to instructor
            if ($lesson->instructor?->user) {
                $handlerName = $lesson->handler?->full_name ?? 'a handler';
                app(MessageService::class)->createDirect(
                    1,
                    $lesson->instructor->user->id,
                    "Reminder: Private Lesson Tomorrow — {$dogName}",
                    [['type' => 'text', 'content' => "Reminder: you have a private lesson with {$handlerName} ({$dogName}) tomorrow, {$date} at {$time}."]]
                );
                $this->info("Instructor reminder sent to {$lesson->instructor->user->email} for {$dogName}");
                $sent++;
            }
        }

        $this->info("Done — {$sent} reminder message(s) sent for {$lessons->count()} lesson(s).");
    }
}
