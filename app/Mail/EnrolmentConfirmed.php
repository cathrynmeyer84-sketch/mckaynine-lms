<?php

namespace App\Mail;

use App\Models\BranchSetting;
use App\Models\ClassType;
use App\Models\EmailTemplate;
use App\Models\Enrolment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

class EnrolmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;
    public string $viewTemplate;

    public function __construct(public Enrolment $enrolment)
    {
        $handler  = $enrolment->handler;
        $dog      = $enrolment->dog;
        $class    = $enrolment->dogClass;
        $branch   = BranchSetting::current();
        $isAssessmentPathway = $enrolment->pathway === 'assessment';

        $token    = Password::createToken($handler->user);
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $handler->user->email], false)) . '&setup=1';
        $pwaUrl   = config('app.url');

        // Build fee line (only relevant when class is assigned)
        $coursePrice  = $class?->classType?->course_price ?? $class?->course_price ?? null;
        $enrolmentFee = $class?->enrolment_fee ?? $branch->enrolment_fee ?? null;
        $feeLines = [];
        if ($coursePrice)  $feeLines[] = 'Course fee: R' . number_format($coursePrice, 2);
        if ($enrolmentFee) $feeLines[] = 'Enrolment fee: R' . number_format($enrolmentFee, 2);
        $classFee = !empty($feeLines) ? implode("\n", $feeLines) : '';

        // Build recommended classes block for assessment pathway
        $recommendedClassesHtml = '';
        if ($isAssessmentPathway) {
            $score = $enrolment->assessmentRequest?->scores;
            $ids   = $score?->recommended_class_ids ?? [];

            if (!empty($ids)) {
                $classTypes = ClassType::whereIn('id', $ids)
                    ->where('info_page_enabled', true)
                    ->get();

                if ($classTypes->isNotEmpty()) {
                    $recommendedClassesHtml .= '<div style="margin:20px 0;">';
                    foreach ($classTypes as $ct) {
                        $infoUrl = url(route('class-info.show', $ct->slug));
                        $recommendedClassesHtml .=
                            '<div style="background:#f5f1ea;border-radius:8px;padding:14px 16px;margin-bottom:10px;">'
                            . '<div style="font-weight:700;color:#1A1D2E;font-size:15px;">' . e($ct->name) . '</div>'
                            . ($ct->tagline ? '<div style="font-size:13px;color:#6b7280;margin:3px 0 8px;">' . e($ct->tagline) . '</div>' : '<div style="margin-bottom:8px;"></div>')
                            . '<a href="' . $infoUrl . '" style="display:inline-block;background:#1A1D2E;color:#fff;text-decoration:none;padding:6px 14px;border-radius:6px;font-size:13px;font-weight:600;">View Class Info →</a>'
                            . '</div>';
                    }
                    $recommendedClassesHtml .= '</div>';
                }
            }
        }

        // Choose template key and view
        if ($isAssessmentPathway) {
            $templateKey  = 'assessment_enrolment_confirmed';
            $this->viewTemplate = 'emails.template-html';
        } else {
            $templateKey  = 'enrolment_confirmed';
            $this->viewTemplate = 'emails.template-plain';
        }

        $template = EmailTemplate::getByKey($templateKey);
        $rendered = $template?->render([
            '{handler_name}'          => $handler->first_name,
            '{dog_name}'              => $dog->name,
            '{class_name}'            => $class?->name ?? '',
            '{class_fee}'             => $classFee,
            '{recommended_classes}'   => $recommendedClassesHtml,
            '{password_setup_link}'   => $isAssessmentPathway
                ? '<a href="' . $resetUrl . '" style="color:#3569BF;font-weight:600;">Set up your password →</a>'
                : $resetUrl,
            '{pwa_link}'              => $pwaUrl,
        ]);

        $this->emailSubject = $rendered['subject'] ?? 'Your enrolment is confirmed!';
        $this->emailBody    = $rendered['body']    ?? '';
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
