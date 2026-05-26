<?php

namespace App\Mail;

use App\Models\AssessmentRequest;
use App\Models\AppSetting;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssessmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;

    public function __construct(public AssessmentRequest $assessmentRequest)
    {
        $handler = $assessmentRequest->handler;
        $dog     = $assessmentRequest->dog;
        $slot    = $assessmentRequest->slot;

        $date     = $slot?->date?->format('l, d F Y') ?? '';
        $time     = $slot ? \Carbon\Carbon::parse($slot->start_time)->format('g:i A') : '';
        if ($slot?->end_time) {
            $time .= ' – ' . \Carbon\Carbon::parse($slot->end_time)->format('g:i A');
        }
        $location = AppSetting::get('assessment_location', 'Please check your confirmation email for the address.');

        $template = EmailTemplate::getByKey('assessment_reminder');
        $rendered = $template?->render([
            '{handler_name}' => $handler?->first_name ?? '',
            '{dog_name}'     => $dog?->name ?? '',
            '{date}'         => $date,
            '{time}'         => $time,
            '{location}'     => $location,
        ]);

        $this->emailSubject = $rendered['subject'] ?? "Reminder: Assessment Tomorrow — {$dog?->name}";
        $this->emailBody    = $rendered['body'] ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.template-plain', with: ['body' => $this->emailBody]);
    }

    public function attachments(): array { return []; }
}
