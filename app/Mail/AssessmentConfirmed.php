<?php

namespace App\Mail;

use App\Models\AssessmentRequest;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssessmentConfirmed extends Mailable
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
        $location = \App\Models\AppSetting::get('assessment_location', 'To be confirmed — we will follow up with details.');

        $template = EmailTemplate::getByKey('assessment_confirmed');
        $rendered = $template?->render([
            '{handler_name}' => $handler?->first_name ?? '',
            '{dog_name}'     => $dog?->name ?? '',
            '{date}'         => $date,
            '{time}'         => $time,
            '{location}'     => $location,
        ]);

        $this->emailSubject = $rendered['subject'] ?? "Assessment Confirmed — {$dog?->name}";
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
