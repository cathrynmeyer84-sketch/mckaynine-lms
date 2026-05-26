<?php

namespace App\Mail;

use App\Models\AssessmentRequest;
use App\Models\AssessmentScore;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AssessmentOutcome extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;
    public string $viewTemplate;

    public function __construct(
        public AssessmentRequest $assessmentRequest,
        public AssessmentScore $score,
    ) {
        $handler = $assessmentRequest->handler;
        $dog     = $assessmentRequest->dog;
        $outcome = $score->final_outcome ?? $score->recommended_outcome ?? 'group_class';

        $templateKey = match($outcome) {
            'private_lessons' => 'assessment_outcome_private',
            'behaviourist'    => 'assessment_outcome_behaviourist',
            default           => 'assessment_outcome_group',
        };

        $isGroup          = $templateKey === 'assessment_outcome_group';
        $isPrivateLessons = $templateKey === 'assessment_outcome_private';
        $this->viewTemplate = ($isGroup || $isPrivateLessons) ? 'emails.template-html' : 'emails.template-plain';

        $replacements = [
            '{handler_name}' => $handler?->first_name ?? '',
            '{dog_name}'     => $dog?->name ?? 'your dog',
        ];

        if ($isGroup) {
            $enrolUrl = URL::signedRoute('enrol.graduate', ['assessmentRequest' => $assessmentRequest->id]);
            $replacements['{enrol_url}'] = '<a href="' . $enrolUrl . '" class="enrol-btn">Enrol Now →</a>';
        }

        if ($isPrivateLessons) {
            $bookUrl = URL::signedRoute('enrol.private-lessons', ['assessmentRequest' => $assessmentRequest->id]);
            $replacements['{private_lesson_url}'] = '<a href="' . $bookUrl . '" class="enrol-btn">Book Your Private Lesson →</a>';
        }

        $template = EmailTemplate::getByKey($templateKey);
        $rendered = $template?->render($replacements);

        $this->emailSubject = $rendered['subject'] ?? match($outcome) {
            'private_lessons' => 'Your Assessment Outcome — Private Lessons Recommended',
            'behaviourist'    => 'Your Assessment Outcome — Next Steps',
            default           => "Great News — {$dog?->name} is Ready for Group Classes!",
        };
        $this->emailBody = $rendered['body'] ?? '';
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->emailSubject);
    }

    public function content(): Content
    {
        return new Content(view: $this->viewTemplate, with: ['body' => $this->emailBody]);
    }

    public function attachments(): array { return []; }
}
