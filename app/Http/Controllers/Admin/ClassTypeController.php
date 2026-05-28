<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{ClassType, ClassTypeWeek, InstructorBriefingItem, GradingExercise, GradingDeductionEvent, GradingRatingScale};
use Illuminate\Http\Request;

class ClassTypeController extends Controller
{
    public function index()
    {
        $classTypes = ClassType::withCount('classes')->with('weeks')->orderBy('name')->get();

        $grouped = [
            'structured' => $classTypes->where('has_structured_content', true)->values(),
            'monthly'    => $classTypes->where('has_structured_content', false)->values(),
        ];

        return view('admin.class-types.index', compact('classTypes', 'grouped'));
    }

    public function create()
    {
        return view('admin.class-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'duration_type'          => 'required|in:term,ongoing',
            'term_weeks'             => 'nullable|integer|min:1|max:52',
            'billing_period'         => 'nullable|in:monthly,yearly',
            'monthly_fee_per_dog'    => 'nullable|numeric|min:0',
            'course_price'           => 'nullable|numeric|min:0',
            'has_structured_content' => 'boolean',
            'has_grading'            => 'boolean',
        ]);

        $classType = ClassType::create([
            'name'                   => $request->name,
            'description'            => $request->description,
            'duration_type'          => $request->duration_type,
            'term_weeks'             => $request->duration_type === 'term' ? $request->term_weeks : null,
            'billing_period'         => $request->duration_type === 'ongoing' ? $request->billing_period : null,
            'monthly_fee_per_dog'    => $request->duration_type === 'ongoing' ? $request->monthly_fee_per_dog : null,
            'course_price'           => $request->duration_type === 'term' ? $request->course_price : null,
            'has_structured_content' => $request->boolean('has_structured_content'),
            'has_grading'            => $request->boolean('has_grading'),
        ]);

        // Auto-create empty week slots for term classes with structured content
        if ($classType->has_structured_content && $classType->duration_type === 'term' && $classType->term_weeks) {
            for ($w = 1; $w <= $classType->term_weeks; $w++) {
                ClassTypeWeek::create(['class_type_id' => $classType->id, 'week_number' => $w]);
            }
        }

        return redirect()->route('admin.class-types.edit', $classType)->with('success', 'Class type created. Add your weekly content below.');
    }

    public function show(ClassType $classType)
    {
        $classType->load(['weeks', 'classes' => fn($q) => $q->orderBy('start_date', 'desc')]);
        return view('admin.class-types.show', compact('classType'));
    }

    public function edit(ClassType $classType)
    {
        $classType->load(['weeks.briefingItems', 'gradingExercises.deductionEvents', 'gradingExercises.ratingScales']);
        $allClassTypes = ClassType::orderBy('name')->get();
        return view('admin.class-types.edit', compact('classType', 'allClassTypes'));
    }

    public function update(Request $request, ClassType $classType)
    {
        $request->validate([
            'name'                        => 'required|string|max:255',
            'duration_type'               => 'required|in:term,ongoing',
            'term_weeks'                  => 'nullable|integer|min:1|max:52',
            'billing_period'              => 'nullable|in:monthly,yearly',
            'monthly_fee_per_dog'         => 'nullable|numeric|min:0',
            'course_price'                => 'nullable|numeric|min:0',
            'has_structured_content'      => 'boolean',
            'has_grading'                 => 'boolean',
            'enrolment_mode'              => 'nullable|in:assessment,direct,enquiry',
            'rosette_image'               => 'nullable|image|max:5120',
            'completion_message'          => 'nullable|string',
            'prerequisite_class_type_ids'   => 'nullable|array',
            'prerequisite_class_type_ids.*' => 'integer|exists:class_types,id',
            'io_prod_code'                  => 'nullable|string|max:100',
        ]);

        $oldWeeks = $classType->term_weeks;

        $update = [
            'name'                        => $request->name,
            'description'                 => $request->description,
            'duration_type'               => $request->duration_type,
            'term_weeks'                  => $request->duration_type === 'term' ? $request->term_weeks : null,
            'billing_period'              => $request->duration_type === 'ongoing' ? $request->billing_period : null,
            'monthly_fee_per_dog'         => $request->duration_type === 'ongoing' ? $request->monthly_fee_per_dog : null,
            'course_price'                => $request->duration_type === 'term' ? $request->course_price : null,
            'has_structured_content'      => $request->boolean('has_structured_content'),
            'has_grading'                 => $request->boolean('has_grading'),
            'enrolment_mode'              => $request->input('enrolment_mode', 'assessment'),
            'completion_message'          => $request->input('completion_message'),
            'prerequisite_class_type_ids' => $request->input('prerequisite_class_type_ids') ?: null,
            'io_prod_code'                => $request->input('io_prod_code') ?: null,
        ];

        if ($request->hasFile('rosette_image')) {
            $update['rosette_image_path'] = $request->file('rosette_image')->store('class-types/rosettes', 'public');
        }

        $classType->update($update);

        // Add new week slots if term_weeks increased
        if ($classType->has_structured_content && $classType->duration_type === 'term' && $classType->term_weeks) {
            $existingWeeks = $classType->weeks()->pluck('week_number')->toArray();
            for ($w = 1; $w <= $classType->term_weeks; $w++) {
                if (!in_array($w, $existingWeeks)) {
                    ClassTypeWeek::create(['class_type_id' => $classType->id, 'week_number' => $w]);
                }
            }
        }

        return back()->with('success', 'Class type updated.');
    }

    public function saveWeek(Request $request, ClassType $classType, ClassTypeWeek $week)
    {
        $week->update($request->only([
            'title', 'description', 'youtube_url',
            'practice_checklist', 'what_to_bring_next_week', 'extra_notes',
        ]));

        return response()->json(['ok' => true]);
    }

    public function storeBriefingItem(Request $request, ClassType $classType, ClassTypeWeek $week)
    {
        $data = $request->validate([
            'exercise_name'  => 'required|string|max:255',
            'description'    => 'nullable|string',
            'suggested_time' => 'nullable|string|max:100',
            'image'          => 'nullable|image|max:5120',
        ]);

        $item = InstructorBriefingItem::create([
            'class_type_week_id' => $week->id,
            'exercise_name'      => $data['exercise_name'],
            'description'        => $data['description'] ?? null,
            'suggested_time'     => $data['suggested_time'] ?? null,
            'sort_order'         => $week->briefingItems()->count(),
        ]);

        if ($request->hasFile('image')) {
            $item->update(['image_path' => $request->file('image')->store('briefing', 'public')]);
        }

        $openWeekId = $request->input('_action') === 'save_and_add' ? $week->id : null;

        return back()
            ->with('success', 'Exercise added.')
            ->with('_tab', 'briefing')
            ->with('_open_week_id', $openWeekId);
    }

    public function updateBriefingItem(Request $request, InstructorBriefingItem $item)
    {
        $data = $request->validate([
            'exercise_name'  => 'required|string|max:255',
            'description'    => 'nullable|string',
            'suggested_time' => 'nullable|string|max:100',
            'image'          => 'nullable|image|max:5120',
        ]);

        $item->update([
            'exercise_name'  => $data['exercise_name'],
            'description'    => $data['description'] ?? null,
            'suggested_time' => $data['suggested_time'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $item->update(['image_path' => $request->file('image')->store('briefing', 'public')]);
        }

        return back()->with('success', 'Exercise updated.')->with('_tab', 'briefing');
    }

    public function destroyBriefingItem(InstructorBriefingItem $item)
    {
        $item->delete();
        return back()->with('success', 'Exercise removed.')->with('_tab', 'briefing');
    }

    // ── Grading Exercises ──

    public function reorderGradingExercise(Request $request, GradingExercise $exercise)
    {
        $direction = $request->input('direction');
        $ids = $exercise->classType->gradingExercises()->orderBy('sort_order')->pluck('id')->toArray();
        $pos = array_search($exercise->id, $ids);

        if ($direction === 'up' && $pos > 0) {
            [$ids[$pos - 1], $ids[$pos]] = [$ids[$pos], $ids[$pos - 1]];
        } elseif ($direction === 'down' && $pos < count($ids) - 1) {
            [$ids[$pos], $ids[$pos + 1]] = [$ids[$pos + 1], $ids[$pos]];
        }

        foreach ($ids as $sortOrder => $id) {
            GradingExercise::where('id', $id)->update(['sort_order' => $sortOrder]);
        }

        return back()->with('_tab', 'grading');
    }

    public function storeGradingExercise(Request $request, ClassType $classType)
    {
        $data = $request->validate([
            'type'                 => 'required|in:marks,rating,time',
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'starting_marks'       => 'nullable|numeric|min:0',
            'target_time_seconds'  => 'nullable|integer|min:1',
            'allow_second_attempt' => 'boolean',
        ]);

        $exercise = GradingExercise::create([
            'class_type_id'        => $classType->id,
            'type'                 => $data['type'],
            'name'                 => $data['name'],
            'description'          => $data['description'] ?? null,
            'starting_marks'       => $data['starting_marks'] ?? null,
            'target_time_seconds'  => $data['target_time_seconds'] ?? null,
            'allow_second_attempt' => $request->boolean('allow_second_attempt'),
            'sort_order'           => $classType->gradingExercises()->count(),
        ]);

        return back()->with('success', 'Exercise added.')->with('_tab', 'grading')->with('_open_exercise_id', $exercise->id);
    }

    public function updateGradingExercise(Request $request, GradingExercise $exercise)
    {
        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'description'          => 'nullable|string',
            'starting_marks'       => 'nullable|numeric|min:0',
            'target_time_seconds'  => 'nullable|integer|min:1',
            'allow_second_attempt' => 'boolean',
        ]);

        $exercise->update([
            'name'                 => $data['name'],
            'description'          => $data['description'] ?? null,
            'starting_marks'       => $data['starting_marks'] ?? null,
            'target_time_seconds'  => $data['target_time_seconds'] ?? null,
            'allow_second_attempt' => $request->boolean('allow_second_attempt'),
        ]);

        return back()->with('success', 'Exercise updated.')->with('_tab', 'grading');
    }

    public function destroyGradingExercise(GradingExercise $exercise)
    {
        $exercise->delete();
        return back()->with('success', 'Exercise removed.')->with('_tab', 'grading');
    }

    // ── Deduction Events (marks-based) ──

    public function storeDeductionEvent(Request $request, GradingExercise $exercise)
    {
        $data = $request->validate([
            'event_name'    => 'required|string|max:255',
            'marks_deducted' => 'required|numeric|min:0',
        ]);

        GradingDeductionEvent::create([
            'grading_exercise_id' => $exercise->id,
            'event_name'          => $data['event_name'],
            'marks_deducted'      => $data['marks_deducted'],
            'sort_order'          => $exercise->deductionEvents()->count(),
        ]);

        $addAnother = $request->input('_action') === 'save_and_add';

        return back()
            ->with('success', 'Event added.')
            ->with('_tab', 'grading')
            ->with('_open_exercise_id', $exercise->id)
            ->with('_add_event_open', $addAnother);
    }

    public function updateDeductionEvent(Request $request, GradingDeductionEvent $event)
    {
        $data = $request->validate([
            'event_name'    => 'required|string|max:255',
            'marks_deducted' => 'required|numeric|min:0',
        ]);

        $event->update($data);

        return back()->with('success', 'Event updated.')->with('_tab', 'grading');
    }

    public function destroyDeductionEvent(GradingDeductionEvent $event)
    {
        $event->delete();
        return back()->with('success', 'Event removed.')->with('_tab', 'grading');
    }

    // ── Rating Scales (rating-based) ──

    public function storeRatingScale(Request $request, GradingExercise $exercise)
    {
        $data = $request->validate([
            'label'             => 'required|string|max:100',
            'description'       => 'nullable|string',
            'marks_deducted'    => 'required|numeric|min:0',
            'is_automatic_fail' => 'boolean',
        ]);

        GradingRatingScale::create([
            'grading_exercise_id' => $exercise->id,
            'label'               => $data['label'],
            'description'         => $data['description'] ?? null,
            'marks_deducted'      => $data['marks_deducted'],
            'is_automatic_fail'   => $request->boolean('is_automatic_fail'),
            'sort_order'          => $exercise->ratingScales()->count(),
        ]);

        $addAnother = $request->input('_action') === 'save_and_add';

        return back()
            ->with('success', 'Rating added.')
            ->with('_tab', 'grading')
            ->with('_open_exercise_id', $exercise->id)
            ->with('_add_rating_open', $addAnother);
    }

    public function updateRatingScale(Request $request, GradingRatingScale $scale)
    {
        $data = $request->validate([
            'label'             => 'required|string|max:100',
            'description'       => 'nullable|string',
            'marks_deducted'    => 'required|numeric|min:0',
            'is_automatic_fail' => 'boolean',
        ]);

        $scale->update([
            'label'             => $data['label'],
            'description'       => $data['description'] ?? null,
            'marks_deducted'    => $data['marks_deducted'],
            'is_automatic_fail' => $request->boolean('is_automatic_fail'),
        ]);

        return back()->with('success', 'Rating updated.')->with('_tab', 'grading');
    }

    public function destroyRatingScale(GradingRatingScale $scale)
    {
        $scale->delete();
        return back()->with('success', 'Rating removed.')->with('_tab', 'grading');
    }

    public function editInfoPage(ClassType $classType)
    {
        return view('admin.class-types.info-page', compact('classType'));
    }

    public function updateInfoPage(Request $request, ClassType $classType)
    {
        $request->merge([
            'slug' => $request->filled('slug') ? $request->input('slug') : null,
        ]);

        $slugRule = $request->filled('slug')
            ? 'nullable|string|max:100|alpha_dash|unique:class_types,slug,' . $classType->id
            : 'nullable';

        $request->validate([
            'slug'                 => $slugRule,
            'page_template'        => 'required|in:puppy,eo_cgc,default',
            'tagline'              => 'nullable|string|max:500',
            'hero_heading'         => 'nullable|string|max:200',
            'about'                => 'nullable|string',
            'general_schedule'     => 'nullable|string|max:500',
            'cost_from'            => 'nullable|numeric|min:0',
            'cost_notes'           => 'nullable|string',
            'promo_video_url'      => 'nullable|url',
            'testimonial_text'     => 'nullable|string',
            'testimonial_name'     => 'nullable|string|max:200',
            'image'                => 'nullable|image|max:10240',
            'image_mobile'         => 'nullable|image|max:10240',
            'testimonial_photo'    => 'nullable|image|max:5120',
            'gallery_add.*'        => 'nullable|image|max:10240',
            'individual_class_pages' => 'boolean',
            'trust_strap'           => 'nullable|string',
            'helps_with'            => 'nullable|string',
            'age_requirements'      => 'nullable|string',
            'what_to_bring'         => 'nullable|string',
            'how_to_join_steps'     => 'nullable|string',
            'joining_notes'         => 'nullable|string',
            'color_theme'           => 'nullable|in:forest,ocean,slate',
            'hero_overlay_color'   => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'cta_type'             => 'required|in:enquire,enrol,assessment',
        ]);

        $infoPageEnabled = $request->boolean('info_page_enabled');
        $slug = $request->input('slug');
        if ($infoPageEnabled && empty($slug)) {
            $slug = \Illuminate\Support\Str::slug($classType->name);
        }

        $isEntryClass = $request->boolean('is_entry_class');

        // Enforce single entry class — clear any other class type that has it set
        if ($isEntryClass) {
            \App\Models\ClassType::where('id', '!=', $classType->id)
                ->where('is_entry_class', true)
                ->update(['is_entry_class' => false]);
        }

        $update = [
            'info_page_enabled'       => $infoPageEnabled,
            'is_public'               => $request->boolean('is_public'),
            'individual_class_pages'  => $request->boolean('individual_class_pages'),
            'is_entry_class'          => $isEntryClass,
            'slug'              => $slug,
            'page_template'     => $request->input('page_template', 'default'),
            'tagline'           => $request->input('tagline'),
            'hero_heading'      => $request->input('hero_heading') ?: null,
            'about'             => $request->input('about'),
            'general_schedule'  => $request->input('general_schedule'),
            'cost_from'         => $request->input('cost_from') ?: null,
            'cost_notes'        => $request->input('cost_notes'),
            'promo_video_url'   => $request->input('promo_video_url'),
            'testimonial_text'   => $request->input('testimonial_text'),
            'testimonial_name'   => $request->input('testimonial_name'),
            'trust_strap'        => $request->input('trust_strap') ?: null,
            'helps_with'         => $request->input('helps_with') ?: null,
            'age_requirements'   => $request->input('age_requirements') ?: null,
            'what_to_bring'      => $request->input('what_to_bring') ?: null,
            'how_to_join_steps'  => $request->input('how_to_join_steps') ?: null,
            'joining_notes'      => $request->input('joining_notes') ?: null,
            'color_theme'        => $request->input('color_theme', 'forest'),
            'hero_overlay_color' => $request->filled('hero_overlay_color') ? $request->input('hero_overlay_color') : null,
            'cta_type'           => $request->input('cta_type', 'enquire'),
        ];

        if ($request->hasFile('image')) {
            $update['image_path'] = $request->file('image')->store('class-types/heroes', 'public');
        }
        if ($request->hasFile('image_mobile')) {
            $update['image_mobile_path'] = $request->file('image_mobile')->store('class-types/heroes', 'public');
        }
        if ($request->hasFile('testimonial_photo')) {
            $update['testimonial_photo_path'] = $request->file('testimonial_photo')->store('class-types/testimonials', 'public');
        }
        if ($request->hasFile('fees_image')) {
            $update['fees_image_path'] = $request->file('fees_image')->store('class-types/fees', 'public');
        }
        if ($request->hasFile('fees_image_mobile')) {
            $update['fees_image_mobile_path'] = $request->file('fees_image_mobile')->store('class-types/fees', 'public');
        }

        // Gallery: append new images, allow removal of existing
        $existing = $classType->gallery_images ?? [];
        $keep     = $request->input('keep_gallery', []);
        $existing = array_values(array_filter($existing, fn($p) => in_array($p, $keep)));

        if ($request->hasFile('gallery_add')) {
            foreach ($request->file('gallery_add') as $file) {
                $existing[] = $file->store('class-types/gallery', 'public');
            }
        }
        $update['gallery_images'] = $existing;

        $classType->update($update);

        return redirect()->route('admin.class-types.edit', $classType)
            ->with('success', 'Info page saved.')
            ->with('_tab', 'info_page');
    }

    public function destroy(ClassType $classType)
    {
        $classType->delete();
        return redirect()->route('admin.class-types.index')->with('success', 'Class type deleted.');
    }
}
