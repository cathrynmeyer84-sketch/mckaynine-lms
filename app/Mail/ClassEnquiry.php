<?php

namespace App\Mail;

use App\Models\{BranchSetting, ClassType, DogClass};
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClassEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data,
        public ClassType $classType,
        public ?DogClass $specificClass,
        public BranchSetting $branch,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->data['email']],
            subject: "New Enquiry: {$this->classType->name}" . ($this->specificClass ? " — {$this->specificClass->name}" : ''),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.class-enquiry');
    }
}
