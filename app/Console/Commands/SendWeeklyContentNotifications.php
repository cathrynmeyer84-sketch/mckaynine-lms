<?php

namespace App\Console\Commands;

use App\Models\ClassDate;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendWeeklyContentNotifications extends Command
{
    protected $signature   = 'classes:send-content-notifications';
    protected $description = 'Send push notifications when weekly class content is due to go out';

    public function handle(PushNotificationService $push): void
    {
        $due = ClassDate::whereNotNull('content_send_date')
            ->whereNull('content_sent_at')
            ->where('content_send_date', '<=', now())
            ->whereHas('weeklyContent', fn($q) => $q->where('is_published', true))
            ->with(['dogClass.confirmedEnrolments.handler.user', 'dogClass'])
            ->get();

        $sent = 0;

        foreach ($due as $classDate) {
            $users = $classDate->dogClass->confirmedEnrolments
                ->map(fn($e) => $e->handler?->user)
                ->filter();

            if ($users->isEmpty()) continue;

            $push->sendToUsers(
                $users,
                'New Class Content',
                "Week {$classDate->week_number} content for {$classDate->dogClass->name} is now available.",
                ['url' => '/my/classes']
            );

            $classDate->update(['content_sent_at' => now()]);
            $sent += $users->count();
        }

        $this->info("Sent content notifications to {$sent} handler(s).");
    }
}
