<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{DogClass, ClassDate, ClassType, Instructor, WeeklyContent, ExamResult, AppNotification};
use App\Services\MessageService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $status  = request('status', 'current');
        $today   = now()->startOfDay();
        $yearAgo = now()->subYear()->startOfDay();

        $classes = DogClass::with(['instructors', 'confirmedEnrolments', 'classType'])
            ->when(request('class_type_id'), fn($q, $id) => $q->where('class_type_id', $id))
            ->when($status, function ($q, $s) use ($today, $yearAgo) {
                match ($s) {
                    'current'   => $q->where('end_date', '>=', $today),
                    'upcoming'  => $q->where('start_date', '>', $today),
                    'active'    => $q->where('start_date', '<=', $today)->where('end_date', '>=', $today),
                    'completed' => $q->where('end_date', '<', $today)->where('end_date', '>=', $yearAgo),
                    'archived'  => $q->where('end_date', '<', $yearAgo),
                    'all'       => null,
                    default     => null,
                };
            })
            ->orderBy('start_date', 'desc')->paginate(20);
        $classTypes = ClassType::orderBy('name')->get();
        return view('admin.classes.index', compact('classes', 'classTypes'));
    }

    public function create()
    {
        $instructors = Instructor::where('is_active', true)->get();
        $classTypes  = ClassType::orderBy('name')->get();
        return view('admin.classes.create', compact('instructors', 'classTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $class = DogClass::create($request->only([
            'name', 'class_type_id', 'has_final_exam', 'max_capacity',
            'start_date', 'end_date', 'start_time', 'end_time', 'location', 'description',
        ]));

        // Assign instructors (no lead)
        if ($request->instructor_ids) {
            $class->instructors()->attach($request->instructor_ids);
        }

        // Auto-generate class dates from calendar
        app(CalendarController::class)->generateClassDates($class);

        return redirect()->route('admin.classes.show', $class)->with('success', 'Class created. Dates generated from calendar.');
    }

    public function show(DogClass $class)
    {
        $class->load(['instructors', 'confirmedEnrolments.dog', 'confirmedEnrolments.handler', 'confirmedEnrolments.assignedInstructor', 'confirmedEnrolments.examResult', 'dates.weeklyContent', 'dates.registers', 'dates.standInInstructor']);
        $allInstructors = Instructor::where('is_active', true)->orderBy('first_name')->get();
        return view('admin.classes.show', compact('class', 'allInstructors'));
    }

    public function edit(DogClass $class)
    {
        $instructors  = Instructor::where('is_active', true)->get();
        $classTypes   = ClassType::orderBy('name')->get();
        $otherClasses = DogClass::where('id', '!=', $class->id)
            ->where('end_date', '>=', now())
            ->with('classType')
            ->orderBy('start_date')
            ->get();
        $class->load(['instructors', 'dates', 'classType']);
        return view('admin.classes.edit', compact('class', 'instructors', 'classTypes', 'otherClasses'));
    }

    public function update(Request $request, DogClass $class)
    {
        $oldStart = $class->start_date?->toDateString();
        $oldEnd   = $class->end_date?->toDateString();
        $oldStart_time = $class->start_time;
        $oldEnd_time   = $class->end_time;

        $class->update(array_merge(
            $request->only([
                'name', 'class_type_id', 'has_final_exam', 'max_capacity',
                'start_date', 'end_date', 'start_time', 'end_time', 'location', 'description',
            ]),
            [
                'next_class_ids'      => $request->input('next_class_ids') ?: [],
                'next_class_type_ids' => $request->input('next_class_type_ids') ?: [],
            ]
        ));

        // Update instructors
        if ($request->has('instructor_ids')) {
            $class->instructors()->sync($request->instructor_ids ?? []);
        }

        // Regenerate dates if schedule-affecting fields changed
        $newStart = $class->fresh()->start_date?->toDateString();
        $newEnd   = $class->fresh()->end_date?->toDateString();
        $newStart_time = $class->fresh()->start_time;
        $newEnd_time   = $class->fresh()->end_time;

        if ($oldStart !== $newStart || $oldEnd !== $newEnd || $oldStart_time !== $newStart_time || $oldEnd_time !== $newEnd_time) {
            app(CalendarController::class)->generateClassDates($class->fresh());
        }

        return redirect()->route('admin.classes.show', $class)->with('success', 'Class updated.');
    }

    public function manageContent(DogClass $class)
    {
        $class->load([
            'dates' => fn($q) => $q->orderBy('date'),
            'dates.weeklyContent',
            'dates.classTypeWeek',
        ]);
        return view('admin.classes.content', compact('class'));
    }

    public function storeContent(Request $request, DogClass $class, ClassDate $classDate)
    {
        $wasPublished = $classDate->weeklyContent?->is_published ?? false;

        $content = WeeklyContent::updateOrCreate(
            ['class_date_id' => $classDate->id],
            $request->only(['title', 'description', 'youtube_url', 'practice_checklist', 'what_to_bring_next_week', 'extra_notes', 'is_published', 'publish_at'])
        );

        if ($request->filled('content_send_date')) {
            $classDate->update(['content_send_date' => $request->content_send_date]);
        }

        $nowPublished = $content->fresh()->is_published;
        if (!$wasPublished && $nowPublished) {
            app(MessageService::class)->broadcastClassContent(
                $class->load(['instructors', 'enrolments.handler.user', 'enrolments.dog']),
                $classDate,
                $content->fresh(),
                auth()->id()
            );
        }

        return back()->with('success', 'Content saved.');
    }

    public function contentSchedule(DogClass $class)
    {
        $class->load(['classType.weeks', 'dates' => fn($q) => $q->where('is_off_week', false)->orderBy('date')]);
        return view('admin.classes.content-schedule', compact('class'));
    }

    public function saveContentSchedule(Request $request, DogClass $class)
    {
        $class->load(['enrolments.handler.user', 'enrolments.dog', 'instructors']);
        $messageService = app(MessageService::class);
        $broadcastCount = 0;

        foreach ($request->schedule ?? [] as $classDateId => $data) {
            $classDate = ClassDate::where('id', $classDateId)->where('class_id', $class->id)->first();
            if (!$classDate) continue;

            $weekId = $data['class_type_week_id'] ?: null;
            $classDate->update(['class_type_week_id' => $weekId]);

            if (!$weekId) continue;

            $template     = $classDate->classTypeWeek;
            $wasPublished = $classDate->weeklyContent?->is_published ?? false;

            $content = WeeklyContent::updateOrCreate(
                ['class_date_id' => $classDate->id],
                [
                    'title'                  => $template->title,
                    'description'            => $template->description,
                    'youtube_url'            => $template->youtube_url,
                    'practice_checklist'     => $template->practice_checklist,
                    'what_to_bring_next_week'=> $template->what_to_bring_next_week,
                    'extra_notes'            => $template->extra_notes,
                    'is_published'           => true,
                ]
            );

            if (!$wasPublished) {
                $messageService->broadcastClassContent($class, $classDate, $content->fresh(), auth()->id());
                $broadcastCount++;
            }
        }

        $msg = $broadcastCount
            ? "Content schedule saved and {$broadcastCount} week(s) published & sent to handlers."
            : 'Content schedule saved.';

        return back()->with('success', $msg);
    }

    public function viewRegister(DogClass $class)
    {
        $class->load(['dates' => fn($q) => $q->orderBy('date'), 'confirmedEnrolments.handler', 'confirmedEnrolments.dog', 'confirmedEnrolments.registers']);
        return view('admin.classes.register', compact('class'));
    }

    public function editInfoPage(DogClass $class)
    {
        return view('admin.classes.info-page', compact('class'));
    }

    public function updateInfoPage(Request $request, DogClass $class)
    {
        // Normalise slug: treat empty string as null to avoid unique constraint issues
        $request->merge([
            'info_slug' => $request->filled('info_slug') ? $request->input('info_slug') : null,
        ]);

        $slugRule = $request->filled('info_slug')
            ? 'nullable|string|max:100|unique:classes,info_slug,' . $class->id
            : 'nullable';

        $request->validate([
            'info_slug'            => $slugRule,
            'enrolment_form_type'  => 'nullable|in:auto,puppy,assessment',
            'enrolment_fee'        => 'nullable|numeric|min:0',
            'course_fee_notes'     => 'nullable|string',
            'fees_image'           => 'nullable|image|max:5120',
            'fees_image_mobile'    => 'nullable|image|max:5120',
            'info_address'         => 'nullable|string|max:500',
            'bank_name'            => 'nullable|string|max:200',
            'bank_account_name'    => 'nullable|string|max:200',
            'bank_account_number'  => 'nullable|string|max:100',
            'bank_branch_code'     => 'nullable|string|max:50',
            'bank_reference_note'  => 'nullable|string|max:500',
            'info_helps_with'      => 'nullable|string',
            'info_what_to_bring'   => 'nullable|string',
            'info_age_requirements'=> 'nullable|string',
            'info_joining_notes'   => 'nullable|string',
            'info_tagline'         => 'nullable|string|max:500',
            'contact_phone'        => 'nullable|string|max:50',
            'contact_email'        => 'nullable|email|max:200',
            'testimonial_text'     => 'nullable|string',
            'testimonial_name'     => 'nullable|string|max:200',
            'hero_image'           => 'nullable|image|max:10240',
            'hero_image_mobile'    => 'nullable|image|max:10240',
            'testimonial_photo'    => 'nullable|image|max:5120',
        ]);

        $infoPageEnabled = $request->boolean('info_page_enabled');
        $slug            = $request->input('info_slug');

        if ($infoPageEnabled && empty($slug)) {
            $slug = \Illuminate\Support\Str::slug($class->name);
        }

        $update = [
            'info_page_enabled'    => $infoPageEnabled,
            'info_slug'            => $slug,
            'show_enrol_button'    => $request->boolean('show_enrol_button'),
            'enrolment_form_type'  => $request->input('enrolment_form_type', 'auto'),
            'enrolment_fee'        => $request->input('enrolment_fee') ?: null,
            'course_fee_notes'     => $this->linesToArray($request->input('course_fee_notes')),
            'info_address'         => $request->input('info_address'),
            'bank_name'            => $request->input('bank_name'),
            'bank_account_name'    => $request->input('bank_account_name'),
            'bank_account_number'  => $request->input('bank_account_number'),
            'bank_branch_code'     => $request->input('bank_branch_code'),
            'bank_reference_note'  => $request->input('bank_reference_note'),
            'info_helps_with'      => $this->linesToArray($request->input('info_helps_with')),
            'info_what_to_bring'   => $this->linesToArray($request->input('info_what_to_bring')),
            'info_age_requirements'=> $request->input('info_age_requirements'),
            'info_joining_notes'   => $request->input('info_joining_notes'),
            'info_tagline'         => $request->input('info_tagline'),
            'contact_phone'        => $request->input('contact_phone'),
            'contact_email'        => $request->input('contact_email'),
            'testimonial_text'     => $request->input('testimonial_text'),
            'testimonial_name'     => $request->input('testimonial_name'),
        ];

        if ($request->hasFile('hero_image')) {
            $update['info_hero_image_path'] = $request->file('hero_image')->store('class-info/heroes', 'public');
        }
        if ($request->hasFile('hero_image_mobile')) {
            $update['info_hero_image_mobile_path'] = $request->file('hero_image_mobile')->store('class-info/heroes', 'public');
        }
        if ($request->hasFile('fees_image')) {
            $update['fees_image_path'] = $request->file('fees_image')->store('class-info/fees', 'public');
        }
        if ($request->hasFile('fees_image_mobile')) {
            $update['fees_image_mobile_path'] = $request->file('fees_image_mobile')->store('class-info/fees', 'public');
        }
        if ($request->hasFile('testimonial_photo')) {
            $update['testimonial_photo_path'] = $request->file('testimonial_photo')->store('class-info/testimonials', 'public');
        }

        $class->update($update);

        return back()->with('success', 'Info page saved.');
    }

    public function markComplete(DogClass $class)
    {
        $classType = $class->classType;

        $enrolments = $class->enrolments()->where('status', 'confirmed')->with('handler.user')->get();

        foreach ($enrolments as $enrolment) {
            ExamResult::updateOrCreate(
                ['enrolment_id' => $enrolment->id],
                [
                    'exam_type'         => 'completion',
                    'achievement_level' => 'completed',
                    'total_score'       => 100,
                    'status'            => 'released',
                    'released_at'       => now(),
                    'graded_by'         => auth()->id(),
                ]
            );

            // Achievement notification to handler
            if ($handlerUser = $enrolment->handler?->user) {
                AppNotification::create([
                    'user_id' => $handlerUser->id,
                    'type'    => 'achievement',
                    'title'   => 'Achievement Unlocked!',
                    'message' => ($enrolment->dog?->name ?? 'Your dog') . ' has completed ' . $class->name . '! Check your Achievements to see your rosette.',
                    'data'    => json_encode(['url' => '/my/achievements']),
                    'is_read' => false,
                ]);
            }
        }

        // Send completion messages via inbox
        app(MessageService::class)->broadcastToClass(
            $class->load('instructors'),
            'Congratulations on completing {{class_name}}!',
            $this->buildCompletionBlocks($class, $classType),
            auth()->id(),
            'system',
            true,
            'completion'
        );

        return back()->with('success', 'Class marked as complete. Rosettes awarded and messages sent.');
    }

    public function destroy(DogClass $class)
    {
        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', "\"{$class->name}\" has been deleted.");
    }

    public function setStandIn(Request $request, DogClass $class, ClassDate $classDate)
    {
        $request->validate([
            'stand_in_instructor_id' => 'nullable|exists:instructors,id',
        ]);

        $classDate->update(['stand_in_instructor_id' => $request->stand_in_instructor_id ?: null]);

        $msg = $request->stand_in_instructor_id
            ? 'Stand-in instructor set.'
            : 'Stand-in instructor removed.';

        return back()->with('success', $msg);
    }

    private function buildCompletionBlocks(DogClass $class, ?ClassType $classType): array
    {
        $blocks = [
            ['type' => 'text', 'content' => "Hi {{handler_name}},\n\nCongratulations — you and {{dog_name}} have completed **{{class_name}}**!\n\n" . ($classType?->completion_message ?? '')],
        ];

        $nextClassIds     = $class->next_class_ids ?? [];
        $nextClassTypeIds = $class->next_class_type_ids ?? [];
        if (!empty($nextClassIds) || !empty($nextClassTypeIds)) {
            $blocks[] = [
                'type'           => 'next_class',
                'class_ids'      => $nextClassIds,
                'class_type_ids' => $nextClassTypeIds,
            ];
        }

        $blocks[] = ['type' => 'text', 'content' => "Thank you for being part of the McKaynine family.\n\nThe McKaynine Team"];

        return $blocks;
    }

    private function linesToArray(?string $text): array
    {
        if (!$text) return [];
        return array_values(array_filter(array_map('trim', explode("\n", $text))));
    }
}
