<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Enrolment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrolmentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;

    public function __construct(public Enrolment $enrolment, public string $reason)
    {
        $handler = $enrolment->handler;
        $dog     = $enrolment->dog;
        $class   = $enrolment->dogClass;

        $template = EmailTemplate::getByKey('enrolment_rejected');
        $rendered = $template?->render([
            '{handler_name}' => $handler->first_name,
            '{dog_name}'     => $dog->name,
            '{class_name}'   => $class?->name ?? '',
            '{reason}'       => $reason,
        ]);

        $this->emailSubject = $rendered['subject'] ?? 'Update on your enrolment';
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
