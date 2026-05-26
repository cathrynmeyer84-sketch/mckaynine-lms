<?php

namespace App\Mail;

use App\Models\AssessmentRequest;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AssessmentBookingInvite extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;

    public function __construct(
        public AssessmentRequest $assessmentRequest,
        public string $bookingUrl,
    ) {
        $handler  = $assessmentRequest->handler;
        $dog      = $assessmentRequest->dog;

        $template = EmailTemplate::getByKey('assessment_booking_invite');
        $rendered = $template?->render([
            '{handler_name}' => $handler?->first_name ?? '',
            '{dog_name}'     => $dog?->name ?? '',
            '{booking_url}'  => $bookingUrl,
        ]);

        $this->emailSubject = $rendered['subject'] ?? "Book Your Assessment — {$dog?->name}";
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
