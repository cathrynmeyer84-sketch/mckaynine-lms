<?php

namespace App\Http\Controllers;

use App\Models\{ClassType, DogClass};
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ClassInfoController extends Controller
{
    // ── Class Type Info Page ───────────────────────────────────────────────

    public function show(string $slug, Request $request)
    {
        $classType = ClassType::where('slug', $slug)->first();

        if ($classType) {
            return $this->showClassType($classType, $request);
        }

        return $this->showClassInstance($slug, $request);
    }

    public function showForClass(string $typeSlug, DogClass $class, Request $request)
    {
        $classType = ClassType::where('slug', $typeSlug)->firstOrFail();

        $isPreview = $request->boolean('preview') && auth()->check() && auth()->user()->is_admin;
        abort_unless($classType->info_page_enabled || $isPreview, 404);
        abort_unless($classType->individual_class_pages || $isPreview, 404);
        abort_unless($class->class_type_id === $classType->id, 404);
        abort_unless($classType->is_public || auth()->check() || $isPreview, 403);

        // Clone classType and overlay any class-specific overrides
        $classType = $this->applyClassOverrides(clone $classType, $class);

        $availableClasses = $classType->availableClasses()->with('scheduledDates')->get();
        $handler          = auth()->check() ? auth()->user()->handler : null;
        $selectedClass    = $class->load(['instructors', 'scheduledDates']);

        return view($this->templateView($classType), compact('classType', 'availableClasses', 'handler', 'selectedClass'));
    }

    private function applyClassOverrides(ClassType $display, DogClass $class): ClassType
    {
        if ($class->info_tagline) $display->tagline = $class->info_tagline;
        if ($class->info_hero_image_path) $display->image_path = $class->info_hero_image_path;
        if ($class->info_hero_image_mobile_path) $display->image_mobile_path = $class->info_hero_image_mobile_path;
        if ($class->testimonial_text) {
            $display->testimonial_text = $class->testimonial_text;
            $display->testimonial_name = $class->testimonial_name;
            $display->testimonial_photo_path = $class->testimonial_photo_path;
        }
        $effectivePrice = $class->classType?->course_price ?? $class->course_price ?? null;
        if ($effectivePrice) $display->cost_from = $effectivePrice;
        if (!empty($class->info_helps_with)) $display->helps_with = implode("\n", $class->info_helps_with);
        if (!empty($class->info_what_to_bring)) $display->what_to_bring = implode("\n", $class->info_what_to_bring);
        if ($class->info_age_requirements) $display->age_requirements = $class->info_age_requirements;
        if ($class->info_joining_notes) $display->joining_notes = $class->info_joining_notes;
        return $display;
    }

    public function enquireForm(string $slug, Request $request)
    {
        $classType = ClassType::where('slug', $slug)->firstOrFail();
        $isPreview = $request->boolean('preview') && auth()->check() && auth()->user()->is_admin;
        abort_unless($classType->info_page_enabled || $isPreview, 404);
        abort_unless($classType->is_public || auth()->check() || $isPreview, 403);

        $specificClass = $request->filled('class_id') ? DogClass::find($request->input('class_id')) : null;
        $user = auth()->user();
        $handler = $user?->handler;

        return view('class-info-enquire', compact('classType', 'specificClass', 'user', 'handler'));
    }

    public function enquire(string $slug, Request $request)
    {
        $classType = ClassType::where('slug', $slug)->firstOrFail();
        abort_unless($classType->info_page_enabled, 404);
        abort_unless($classType->is_public || auth()->check(), 403);

        $specificClass = $request->filled('class_id') ? DogClass::find($request->input('class_id')) : null;

        if (auth()->check()) {
            $user    = auth()->user();
            $handler = $user->handler;

            $name    = $handler ? $handler->first_name . ' ' . $handler->last_name : $user->name;
            $subject = $specificClass
                ? "Enquiry: {$classType->name} — {$specificClass->name}"
                : "Enquiry: {$classType->name}";

            $body = "{$name} has expressed interest in **{$classType->name}** via the class info page.";
            if ($specificClass) {
                $body .= "\n\nSpecific class: {$specificClass->name}";
                if ($specificClass->start_date) {
                    $body .= " (starting {$specificClass->start_date->format('d M Y')})";
                }
            }
            $body .= "\n\nMessage: " . ($request->input('message') ?: '(no message)');
            if ($handler?->contact_number) $body .= "\n\nPhone: {$handler->contact_number}";
            $body .= "\nEmail: {$user->email}";

            app(MessageService::class)->createDirect(
                $user->id,
                \App\Models\User::where('role', 'admin')->first()->id ?? $user->id,
                $subject,
                [['type' => 'text', 'content' => $body]]
            );

            return redirect()->route('handler.inbox.index')
                ->with('success', "Your enquiry about {$classType->name} has been sent.");
        }

        // Guest submission — validate and send email
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'email'       => 'required|email|max:200',
            'phone'       => 'nullable|string|max:50',
            'message'     => 'required|string|max:2000',
            'heard_from'  => 'nullable|string|max:200',
        ]);

        $branch = \App\Models\BranchSetting::current();
        $toEmail = $branch->email ?: config('mail.from.address');

        Mail::to($toEmail)
            ->send(new \App\Mail\ClassEnquiry($validated, $classType, $specificClass, $branch));

        return redirect()->route('class-info.show', $slug)
            ->with('success', 'Thank you for your enquiry! We\'ll be in touch soon.');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function showClassType(ClassType $classType, Request $request)
    {
        $isPreview = $request->boolean('preview') && auth()->check() && auth()->user()->is_admin;
        abort_unless($classType->info_page_enabled || $isPreview, 404);
        abort_unless($classType->is_public || auth()->check() || $isPreview, 403);

        $availableClasses = $classType->availableClasses()->with('scheduledDates')->get();
        $handler          = auth()->check() ? auth()->user()->handler : null;
        $selectedClass    = null;

        return view($this->templateView($classType), compact('classType', 'availableClasses', 'handler', 'selectedClass'));
    }

    private function templateView(ClassType $classType): string
    {
        return match($classType->page_template) {
            'puppy'  => 'class-type-info-puppy',
            'eo_cgc' => 'class-type-info-eo-cgc',
            default  => 'class-type-info-default',
        };
    }

    // ── Legacy class instance info page (old info_slug system) ─────────────

    private function showClassInstance(string $slug, Request $request)
    {
        $class = DogClass::where('info_slug', $slug)
            ->with(['scheduledDates', 'instructors', 'classType'])
            ->firstOrFail();

        $isPreview = $request->boolean('preview') && auth()->check() && auth()->user()->is_admin;
        abort_unless($class->info_page_enabled || $isPreview, 404);

        $mode    = $class->classType?->enrolment_mode ?? 'assessment';
        $handler = auth()->check() ? auth()->user()->handler : null;

        $hasAssessedDog = $handler?->dogs()->whereHas('enrolments', fn($q) => $q->where('status', 'confirmed'))->exists() ?? false;

        $ctaMode = match($mode) {
            'enquiry' => 'enquiry',
            'direct'  => 'direct',
            default   => $handler && $hasAssessedDog ? 'direct' : 'assessment',
        };

        $enrolUrl = match($class->enrolment_form_type) {
            'puppy'      => route('enrol.puppy') . '?class_id=' . $class->id,
            'assessment' => route('enrol.assessment'),
            default      => route('enrol.start') . '?class_id=' . $class->id,
        };

        return view('class-info', compact('class', 'enrolUrl', 'mode', 'ctaMode', 'handler'));
    }
}
