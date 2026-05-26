<?php

namespace App\Mail;

use App\Models\AssessmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAssessmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AssessmentRequest $assessmentRequest) {}

    public function envelope(): Envelope
    {
        $name = $this->assessmentRequest->handler?->first_name . ' ' . $this->assessmentRequest->handler?->last_name;
        return new Envelope(subject: "New Assessment Request — {$name}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.new-assessment-notification');
    }
}
