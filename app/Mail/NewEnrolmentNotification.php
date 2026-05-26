<?php

namespace App\Mail;

use App\Models\Dog;
use App\Models\DogClass;
use App\Models\Handler;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewEnrolmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Handler  $handler,
        public Dog      $dog,
        public DogClass $dogClass,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New enrolment request — ' . $this->dog->name . ' (' . $this->handler->first_name . ' ' . $this->handler->last_name . ')',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.template-plain', with: [
            'body' => implode("\n\n", [
                'A new enrolment request has been submitted.',
                'Handler: ' . $this->handler->first_name . ' ' . $this->handler->last_name,
                'Dog: ' . $this->dog->name . ($this->dog->breed ? ' (' . $this->dog->breed . ')' : ''),
                'Class requested: ' . $this->dogClass->name,
                'Please log in to the admin panel to review and confirm this enrolment.',
            ]),
        ]);
    }

    public function attachments(): array { return []; }
}
