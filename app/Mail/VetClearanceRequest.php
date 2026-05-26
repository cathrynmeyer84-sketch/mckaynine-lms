<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Enrolment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VetClearanceRequest extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;

    public function __construct(public Enrolment $enrolment)
    {
        $handler   = $enrolment->handler;
        $dog       = $enrolment->dog;
        $class     = $enrolment->dogClass;
        $uploadUrl = url(route('handler.vet-clearance.upload', $enrolment));
        $pdfUrl    = url(route('vet-clearance.pdf'));

        $template = EmailTemplate::getByKey('vet_clearance_request');
        $rendered = $template?->render([
            '{handler_name}'         => $handler->first_name,
            '{dog_name}'             => $dog->name,
            '{class_name}'           => $class?->name ?? '',
            '{vet_clearance_pdf_link}'=> $pdfUrl,
            '{upload_link}'          => $uploadUrl,
        ]);

        $this->emailSubject = $rendered['subject'] ?? 'Vet clearance certificate required';
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
