<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Enrolment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClassAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;
    public array  $classDates;
    public string $classUrl;
    public string $resourcesUrl;

    public function __construct(public Enrolment $enrolment)
    {
        $handler  = $enrolment->handler;
        $dog      = $enrolment->dog;
        $class    = $enrolment->dogClass->load('scheduledDates');

        $this->classDates   = $class->scheduledDates->map(function ($d) use ($class) {
            $time = $class->start_time ? ' at ' . \Carbon\Carbon::parse($class->start_time)->format('g:ia') : '';
            return $d->date->format('l, d M Y') . $time;
        })->toArray();

        $this->classUrl     = url(route('handler.classes.show', $enrolment, false));
        $this->resourcesUrl = url(route('handler.resources.index', [], false));

        $templateKey = $enrolment->pathway === 'assessment' ? 'assessment_class_confirmed' : 'class_assigned';

        $template = EmailTemplate::getByKey($templateKey);
        $rendered = $template?->render([
            '{handler_name}'   => $handler->first_name,
            '{dog_name}'       => $dog->name,
            '{class_name}'     => $class->name,
            '{class_location}' => $class->location ?? '',
        ]);

        $this->emailSubject = $rendered['subject'] ?? 'Your class details are confirmed!';
        $this->emailBody    = $rendered['body']    ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.class-assigned');
    }

    public function attachments(): array { return []; }
}
