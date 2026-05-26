<?php

namespace App\Mail;

use App\Models\{EmailTemplate, CalendarDay, ClassDate, Enrolment};
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OffWeekReminder extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;

    public function __construct(
        public Enrolment   $enrolment,
        public CalendarDay $offDay,
        public ClassDate   $classDate,
    ) {
        $handler = $enrolment->handler;
        $dog     = $enrolment->dog;
        $class   = $enrolment->dogClass;

        $handlerName   = $handler?->first_name ?? 'there';
        $dogName       = $dog?->name ?? 'your dog';
        $className     = $class?->name ?? 'your class';
        $offDate       = $offDay->date->format('l, d F Y');
        $offReason     = $offDay->label ?: 'a scheduled break';

        $nextActive = ClassDate::where('class_id', $class->id)
            ->where('date', '>', $classDate->date->toDateString())
            ->where('is_off_week', false)
            ->orderBy('date')
            ->first();
        $nextClassDate = $nextActive ? $nextActive->date->format('l, d F Y') : 'TBC';

        $template = EmailTemplate::getByKey('off_week_reminder');
        $rendered = $template?->render([
            '{handler_name}'   => $handlerName,
            '{dog_name}'       => $dogName,
            '{class_name}'     => $className,
            '{off_date}'       => $offDate,
            '{off_reason}'     => $offReason,
            '{next_class_date}' => $nextClassDate,
        ]);

        $this->emailSubject = $rendered['subject'] ?? "No class on {$offDate} — {$offReason}";
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
