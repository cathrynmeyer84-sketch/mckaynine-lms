<?php

namespace App\Console\Commands;

use App\Mail\AssessmentReminder;
use App\Models\AssessmentRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAssessmentReminders extends Command
{
    protected $signature   = 'assessments:send-reminders';
    protected $description = 'Send reminder emails to handlers with assessments scheduled for tomorrow';

    public function handle(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        $requests = AssessmentRequest::where('status', 'booked')
            ->whereHas('slot', fn($q) => $q->whereDate('date', $tomorrow))
            ->with(['handler.user', 'dog', 'slot'])
            ->get();

        foreach ($requests as $ar) {
            $email = $ar->handler?->user?->email;
            if (!$email) continue;

            Mail::to($email)->send(new AssessmentReminder($ar));
            $this->info("Reminder sent to {$email} for {$ar->dog?->name}");
        }

        $this->info("Done — {$requests->count()} reminder(s) sent.");
    }
}
