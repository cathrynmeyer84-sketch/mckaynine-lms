<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [

            // ── Sent on first account creation ─────────────────────────────
            [
                'slug'    => 'app_welcome',
                'name'    => 'App Welcome (First Sign-Up)',
                'subject' => 'Welcome to McKaynine, {{handler_name}}!',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nWelcome to the McKaynine app! This is your home base for everything to do with your classes — booking, content, results, and messaging us directly.\n\nHere's a quick overview of what you can do:"],
                    ['type' => 'checklist', 'title' => 'What You Can Do Here', 'items' => [
                        'View your enrolled classes and weekly content',
                        'Track your dog\'s progress and achievements',
                        'Message us directly from your inbox',
                        'Receive class notes and practice guides after each session',
                    ]],
                    ['type' => 'text', 'content' => "**Add the app to your home screen** for the best experience:\n\n📱 **iPhone/iPad (Safari):** Tap the Share button at the bottom of your screen, then tap \"Add to Home Screen\".\n\n🤖 **Android (Chrome):** Tap the three-dot menu in the top right, then tap \"Add to Home Screen\" or \"Install App\"."],
                    ['type' => 'button', 'label' => 'Go to My Classes', 'url' => '/my'],
                    ['type' => 'text', 'content' => "If you have any questions, just reply to this message and we'll get back to you.\n\nWarm regards,\nThe McKaynine Team"],
                ],
            ],

            // ── Sent when enrolment is confirmed with a class assigned ──────
            [
                'slug'    => 'class_confirmation',
                'name'    => 'Class Confirmation',
                'subject' => 'You\'re confirmed for {{class_name}}!',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nGreat news — your place in **{{class_name}}** is confirmed. We're looking forward to seeing you and {{dog_name}}!"],
                    ['type' => 'divider'],
                    ['type' => 'text', 'content' => "**Your class details:**"],
                    ['type' => 'class_info'],
                    ['type' => 'divider'],
                    ['type' => 'text', 'content' => "After each session you'll receive your class notes and practice guide right here in the app. Keep an eye on your inbox!\n\nSee you soon,\nThe McKaynine Team"],
                ],
            ],

            // ── Sent when weekly content is published ───────────────────────
            [
                'slug'    => 'class_content',
                'name'    => 'Weekly Class Content',
                'subject' => 'Week {{week_number}} — {{class_name}}',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nHere's a summary of what we covered in class this week and your practice guide."],
                    ['type' => 'class_content'],
                    ['type' => 'text', 'content' => "See you next week — keep up the great work with {{dog_name}}!\n\nThe McKaynine Team"],
                ],
            ],

            // ── Sent on off weeks ───────────────────────────────────────────
            [
                'slug'    => 'off_week',
                'name'    => 'Off Week Notice',
                'subject' => 'No class this week — {{class_name}}',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nJust a reminder that there is **no class this week** for {{class_name}}.\n\n{{off_week_reason}}\n\nWe'll see you and {{dog_name}} back next week!"],
                    ['type' => 'text', 'content' => "The McKaynine Team"],
                ],
            ],

            // ── Non-graded class completion ─────────────────────────────────
            [
                'slug'    => 'completion',
                'name'    => 'Course Completion (Non-Graded)',
                'subject' => 'Congratulations on completing {{class_name}}!',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nCongratulations — you and {{dog_name}} have completed **{{class_name}}**! It's been wonderful having you both with us.\n\n{{completion_message}}"],
                    ['type' => 'divider'],
                    ['type' => 'next_class'],
                    ['type' => 'text', 'content' => "Thank you for being part of the McKaynine family.\n\nThe McKaynine Team"],
                ],
            ],

            // ── Graded completion: Merit Pass ───────────────────────────────
            [
                'slug'    => 'completion_merit',
                'name'    => 'Course Completion — Merit Pass',
                'subject' => 'Merit Pass — {{class_name}} 🎉',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nOutstanding work — you and {{dog_name}} have achieved a **Merit Pass** in **{{class_name}}**! This is an exceptional result and you should be very proud of how far you've both come.\n\nYour full result and feedback are available in the app."],
                    ['type' => 'button', 'label' => 'View My Result', 'url' => '/my/achievements'],
                    ['type' => 'divider'],
                    ['type' => 'text', 'content' => "Ready for the next challenge? Here's what we recommend for you and {{dog_name}}:"],
                    ['type' => 'next_class'],
                    ['type' => 'text', 'content' => "Congratulations again — it's been a pleasure working with you.\n\nThe McKaynine Team"],
                ],
            ],

            // ── Graded completion: Pass ─────────────────────────────────────
            [
                'slug'    => 'completion_pass',
                'name'    => 'Course Completion — Pass',
                'subject' => 'Congratulations — {{class_name}} Passed!',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nCongratulations — you and {{dog_name}} have successfully passed **{{class_name}}**! You've worked hard and it shows. We're really proud of the progress you've made together.\n\nYour full result and feedback are available in the app."],
                    ['type' => 'button', 'label' => 'View My Result', 'url' => '/my/achievements'],
                    ['type' => 'divider'],
                    ['type' => 'text', 'content' => "Here's what we recommend as your next step with {{dog_name}}:"],
                    ['type' => 'next_class'],
                    ['type' => 'text', 'content' => "Well done — we look forward to seeing you both continue to grow.\n\nThe McKaynine Team"],
                ],
            ],

            // ── Graded completion: Review ───────────────────────────────────
            [
                'slug'    => 'completion_review',
                'name'    => 'Course Completion — Review',
                'subject' => '{{class_name}} — Your Result',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nThank you for completing **{{class_name}}** with {{dog_name}}. Your result has been marked as **Review**, which means you've met most of the requirements but there are a few areas we'd like to see strengthened before progressing.\n\nYour full result and instructor feedback are available in the app — please take a moment to read through the notes."],
                    ['type' => 'button', 'label' => 'View My Result & Feedback', 'url' => '/my/achievements'],
                    ['type' => 'divider'],
                    ['type' => 'text', 'content' => "We'd love to chat with you about next steps — please don't hesitate to reply to this message if you'd like to discuss your result.\n\nThe McKaynine Team"],
                ],
            ],

            // ── Graded completion: Fail ─────────────────────────────────────
            [
                'slug'    => 'completion_fail',
                'name'    => 'Course Completion — Fail',
                'subject' => '{{class_name}} — Your Result',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nThank you for completing **{{class_name}}** with {{dog_name}}. We know how much effort you've both put in, and we appreciate your commitment.\n\nUnfortunately your result for this course was not a pass. Please view your full result and instructor feedback in the app for details on the areas to focus on."],
                    ['type' => 'button', 'label' => 'View My Result & Feedback', 'url' => '/my/achievements'],
                    ['type' => 'divider'],
                    ['type' => 'text', 'content' => "We'd encourage you not to be discouraged — many handlers find that a repeat course makes a huge difference. Please reply to this message if you'd like to talk through your options and next steps.\n\nThe McKaynine Team"],
                ],
            ],

            // ── Exam result released (generic — used for assessment results) ─
            [
                'slug'    => 'result_released',
                'name'    => 'Exam Result Released',
                'subject' => 'Your {{class_name}} result is ready',
                'blocks'  => [
                    ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nYour grading result for **{{class_name}}** has been released. You can view your full scoresheet and feedback in the app."],
                    ['type' => 'button', 'label' => 'View My Result', 'url' => '/my/achievements'],
                    ['type' => 'text', 'content' => "Well done to you and {{dog_name}} on completing the assessment!\n\nThe McKaynine Team"],
                ],
            ],

        ];

        foreach ($templates as $data) {
            MessageTemplate::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // Remove the old 'welcome' template now replaced by 'class_confirmation'
        MessageTemplate::where('slug', 'welcome')->delete();
    }
}
