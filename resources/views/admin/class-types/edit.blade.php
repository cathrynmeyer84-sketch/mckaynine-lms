@extends('layouts.app')

@section('title', 'Edit Class Type')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $classType->name }}</h1>
        <p class="page-subtitle">Edit class type settings and weekly content</p>
    </div>
    <a href="{{ route('admin.class-types.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content" x-data="{ tab: '{{ session('_tab', 'details') }}' }" x-init="tab = '{{ session('_tab', 'details') }}'">

@if(session('success'))
<div class="alert alert-success mb-4">{{ session('success') }}</div>
@endif

{{-- Tab nav --}}
<div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
    <button @click="tab='details'" :class="tab==='details' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Class Details</button>
    @if($classType->has_structured_content && $classType->weeks->isNotEmpty())
    <button @click="tab='content'" :class="tab==='content' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Class Content</button>
    <button @click="tab='briefing'" :class="tab==='briefing' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Instructor Briefing</button>
    @endif
    @if($classType->has_grading)
    <button @click="tab='grading'" :class="tab==='grading' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Grading</button>
    @endif
    <button @click="tab='info_page'" :class="tab==='info_page' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
        class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Info Page</button>
</div>

{{-- ── TAB 1: CLASS DETAILS ── --}}
<div x-show="tab==='details'">
    <form action="{{ route('admin.class-types.update', $classType) }}" method="POST" enctype="multipart/form-data"
        x-data="{
            durationType: '{{ $classType->duration_type }}',
            hasContent: {{ $classType->has_structured_content ? 'true' : 'false' }},
        }">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Basic Info</h2>
            <div class="space-y-4">
                <div>
                    <label class="label">Class Type Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $classType->name) }}" class="input" required>
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea name="description" rows="2" class="input">{{ old('description', $classType->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- InvoicesOnline --}}
        <div class="card mb-6">
            <h2 class="card-title mb-1">InvoicesOnline</h2>
            <p class="text-xs text-gray-400 mb-4">The product/line code used when automatically generating invoices for this class type.</p>
            <div class="max-w-xs">
                <label class="label">Product Code</label>
                <input type="text" name="io_prod_code" class="input font-mono"
                    placeholder="e.g. PUPPY01"
                    value="{{ old('io_prod_code', $classType->io_prod_code) }}">
                <p class="text-xs text-gray-400 mt-1">Must match exactly the code in your InvoicesOnline inventory.</p>
            </div>
        </div>

        {{-- Course Structure --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Course Structure</h2>
            <div class="space-y-4">
                <div>
                    <label class="label">Duration Type <span class="text-red-400">*</span></label>
                    <div class="flex gap-4 mt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="duration_type" value="term" x-model="durationType" class="text-navy"
                                {{ $classType->duration_type === 'term' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">Term (fixed weeks)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="duration_type" value="ongoing" x-model="durationType" class="text-navy"
                                {{ $classType->duration_type === 'ongoing' ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">Ongoing (monthly / yearly)</span>
                        </label>
                    </div>
                </div>

                <div x-show="durationType === 'term'" class="space-y-4">
                    <div>
                        <label class="label">Number of Weeks</label>
                        <div class="flex items-center gap-3 mt-1">
                            <input type="number" name="term_weeks" value="{{ old('term_weeks', $classType->term_weeks) }}"
                                min="1" max="52" class="input w-28">
                            <span class="text-sm text-gray-500">weeks per term</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Increasing this will add new empty week slots.</p>
                    </div>
                    <div>
                        <label class="label">Course Price per Term</label>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm text-gray-500">R</span>
                            <input type="number" name="course_price" step="0.01" min="0"
                                value="{{ old('course_price', $classType->course_price) }}"
                                class="input w-36" placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Standard price charged per dog for a full term. Used in instructor fee calculations.</p>
                    </div>
                </div>

                <div x-show="durationType === 'ongoing'" class="space-y-4">
                    <div>
                        <label class="label">Billing Period</label>
                        <select name="billing_period" class="input w-48 mt-1">
                            <option value="monthly" {{ $classType->billing_period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ $classType->billing_period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Monthly Fee per Dog</label>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm text-gray-500">R</span>
                            <input type="number" name="monthly_fee_per_dog" step="0.01" min="0"
                                value="{{ old('monthly_fee_per_dog', $classType->monthly_fee_per_dog) }}"
                                class="input w-36" placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Standard fee charged per dog per month. Used in instructor fee calculations.</p>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="has_structured_content" value="1" x-model="hasContent"
                            class="mt-0.5 text-navy" {{ $classType->has_structured_content ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium text-gray-700">This course has structured weekly content</p>
                            <p class="text-xs text-gray-400 mt-0.5">Videos, notes, and practice tasks sent to handlers each week</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="has_grading" value="1"
                            class="mt-0.5 text-navy" {{ $classType->has_grading ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-medium text-gray-700">This course has a grading / final exam</p>
                            <p class="text-xs text-gray-400 mt-0.5">Enables the Grading tab to configure exercises and scoring</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Enrolment & Rosette --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Enrolment & Achievements</h2>
            <div class="space-y-4">
                <div>
                    <label class="label">Enrolment Mode</label>
                    <p class="text-xs text-gray-400 mt-0.5 mb-2">Controls the call-to-action on the public info page.</p>
                    <div class="space-y-2">
                        @foreach([
                            'assessment' => ['Assessment required', 'Public sees "Book Assessment". Logged-in handler with assessed dog can sign up.'],
                            'direct'     => ['Direct sign-up', 'Anyone can sign up (e.g. puppy classes). No assessment needed.'],
                            'enquiry'    => ['Enquiry only', 'No direct sign-up — visitors send an enquiry regardless of login (e.g. K9 Yoga).'],
                        ] as $val => [$label, $hint])
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input type="radio" name="enrolment_mode" value="{{ $val }}" class="mt-0.5 text-navy"
                                {{ old('enrolment_mode', $classType->enrolment_mode ?? 'assessment') === $val ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $label }}</p>
                                <p class="text-xs text-gray-400">{{ $hint }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="label">Prerequisites</label>
                    <p class="text-xs text-gray-400 mt-0.5 mb-2">Dogs must have completed <strong>at least one</strong> of the selected class types (either/or, not all).</p>
                    <div class="space-y-2">
                        @foreach($allClassTypes->where('id', '!=', $classType->id) as $ct)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                name="prerequisite_class_type_ids[]"
                                value="{{ $ct->id }}"
                                class="text-navy"
                                {{ in_array($ct->id, $classType->prerequisite_class_type_ids ?? []) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $ct->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="label">Completion Rosette Image</label>
                    <p class="text-xs text-gray-400 mt-0.5 mb-2">Shown on the handler's achievements page on pass, merit pass, or course completion.</p>
                    @if($classType->rosette_image_path)
                    <div class="flex items-center gap-4 mb-3">
                        <img src="{{ Storage::url($classType->rosette_image_path) }}" alt="Rosette" class="h-20 w-20 object-contain rounded-xl border border-gray-200">
                        <p class="text-xs text-gray-500">Current rosette. Upload a new image to replace it.</p>
                    </div>
                    @endif
                    <input type="file" name="rosette_image" accept="image/*" class="input">
                </div>
            </div>
        </div>

        {{-- Completion Message (term classes without grading) --}}
        <div x-show="durationType === 'term' && !{{ $classType->has_grading ? 'true' : 'false' }}" class="card mb-6">
            <h2 class="card-title mb-4">Completion Message</h2>
            <div>
                <label class="label">Message Template</label>
                <p class="text-xs text-gray-400 mt-0.5 mb-2">Sent to handlers when you mark this class as complete. Next class links are appended automatically.</p>
                <textarea name="completion_message" rows="5" class="input"
                    placeholder="Congratulations on completing the course! We hope you and your dog had a fantastic time...">{{ old('completion_message', $classType->completion_message) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Which specific classes to suggest is set on each individual class (Edit Class → Next Class Options).</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>

    @if($classType->has_structured_content && $classType->weeks->isEmpty())
    <div class="card text-center py-8 mt-4">
        <p class="text-gray-500 text-sm">No week slots yet. Set the number of weeks above and save to generate them.</p>
    </div>
    @endif
</div>

{{-- ── TAB 2: CLASS CONTENT ── --}}
@if($classType->has_structured_content && $classType->weeks->isNotEmpty())
<div x-show="tab==='content'" class="space-y-4">
    <p class="text-sm text-gray-500">Set the content that goes out to handlers each week. This is the template — individual classes can customise send dates.</p>

    @foreach($classType->weeks as $week)
    <div class="card" x-data="{ open: {{ $loop->first ? 'true' : 'false' }}, saving: false, saved: false }" id="week-{{ $week->id }}">

        <div class="flex items-center gap-3 cursor-pointer" @click="open = !open">
            <div class="w-8 h-8 rounded-full bg-navy/10 flex items-center justify-center shrink-0">
                <span class="text-xs font-bold text-navy">{{ $week->week_number }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-navy text-sm">
                    Week {{ $week->week_number }}
                    @if($week->title)
                    — <span class="text-gray-600">{{ $week->title }}</span>
                    @else
                    <span class="text-gray-400 font-normal">— no title yet</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="saved" class="text-xs text-green-600" x-cloak>Saved ✓</span>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-cloak class="mt-5 pt-5 border-t border-stone/20 space-y-4">
            <div>
                <label class="label">Week title</label>
                <input type="text" id="title-{{ $week->id }}" value="{{ $week->title }}"
                    placeholder="e.g. Introduction & Sit/Stay" class="input w-full">
            </div>
            <div>
                <label class="label">Description / notes for handlers</label>
                <textarea id="description-{{ $week->id }}" rows="4"
                    placeholder="What will be covered this week..." class="input w-full">{{ $week->description }}</textarea>
            </div>
            <div>
                <label class="label">YouTube video URL</label>
                <input type="url" id="youtube_url-{{ $week->id }}" value="{{ $week->youtube_url }}"
                    placeholder="https://www.youtube.com/watch?v=..." class="input w-full">
            </div>
            <div>
                <label class="label">Practice checklist</label>
                <textarea id="practice_checklist-{{ $week->id }}" rows="4"
                    placeholder="- Practice sit for 5 minutes daily&#10;- Reward calm behaviour"
                    class="input w-full font-mono text-sm">{{ $week->practice_checklist }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Start each item with a dash (-)</p>
            </div>
            <div>
                <label class="label">What to bring next week</label>
                <textarea id="what_to_bring_next_week-{{ $week->id }}" rows="2"
                    placeholder="High value treats, long line lead..." class="input w-full">{{ $week->what_to_bring_next_week }}</textarea>
            </div>
            <div>
                <label class="label">Extra notes (admin only)</label>
                <textarea id="extra_notes-{{ $week->id }}" rows="2"
                    placeholder="Internal notes..." class="input w-full">{{ $week->extra_notes }}</textarea>
            </div>

            <div class="pt-2">
                <button type="button"
                    :disabled="saving || saved"
                    :class="saved ? 'btn btn-sm bg-green-600 text-white border-green-600' : 'btn btn-primary btn-sm'"
                    @click="
                        saving = true; saved = false;
                        fetch('{{ route('admin.class-types.weeks.save', [$classType, $week]) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({
                                title: document.getElementById('title-{{ $week->id }}').value,
                                description: document.getElementById('description-{{ $week->id }}').value,
                                youtube_url: document.getElementById('youtube_url-{{ $week->id }}').value,
                                practice_checklist: document.getElementById('practice_checklist-{{ $week->id }}').value,
                                what_to_bring_next_week: document.getElementById('what_to_bring_next_week-{{ $week->id }}').value,
                                extra_notes: document.getElementById('extra_notes-{{ $week->id }}').value,
                            })
                        })
                        .then(r => r.json())
                        .then(() => { saving = false; saved = true; })
                        .catch(() => { saving = false; alert('Save failed — please try again.'); })
                    ">
                    <span x-show="saving" x-cloak>Saving…</span>
                    <span x-show="saved" x-cloak>✓ Saved</span>
                    <span x-show="!saving && !saved">Save Week {{ $week->week_number }}</span>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── TAB 3: INSTRUCTOR BRIEFING ── --}}
<div x-show="tab==='briefing'" class="space-y-4">
    <p class="text-sm text-gray-500">Set up the exercises and activities for each week. Instructors will see this before and during class.</p>

    @foreach($classType->weeks as $week)
    @php $autoOpen = session('_open_week_id') == $week->id; @endphp
    <div class="card" x-data="{ open: {{ ($loop->first || $autoOpen) ? 'true' : 'false' }}, addOpen: {{ $autoOpen ? 'true' : 'false' }} }"
        x-init="{{ $autoOpen ? '$nextTick(() => { document.getElementById(\'add-form-' . $week->id . '\').scrollIntoView({ behavior: \'smooth\', block: \'start\' }) })' : '' }}"
        id="briefing-week-{{ $week->id }}">

        <div class="flex items-center gap-3 cursor-pointer" @click="open = !open">
            <div class="w-8 h-8 rounded-full bg-navy/10 flex items-center justify-center shrink-0">
                <span class="text-xs font-bold text-navy">{{ $week->week_number }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-navy text-sm">
                    Week {{ $week->week_number }}
                    @if($week->title) — <span class="text-gray-600">{{ $week->title }}</span>@endif
                </p>
                <p class="text-xs text-gray-400">{{ $week->briefingItems->count() }} {{ Str::plural('exercise', $week->briefingItems->count()) }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="open ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-gray-100 space-y-3">

            @forelse($week->briefingItems as $item)
            <div class="bg-gray-50 rounded-xl p-4">
                <form method="POST" action="{{ route('admin.briefing-items.update', $item) }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label text-xs">Exercise Name</label>
                            <input type="text" name="exercise_name" value="{{ $item->exercise_name }}" class="input w-full" required>
                        </div>
                        <div>
                            <label class="label text-xs">Suggested Time</label>
                            <input type="text" name="suggested_time" value="{{ $item->suggested_time }}" class="input w-full" placeholder="e.g. 10 minutes">
                        </div>
                    </div>
                    <div>
                        <label class="label text-xs">Description</label>
                        <textarea name="description" rows="3" class="input w-full" placeholder="Instructions for the instructor...">{{ $item->description }}</textarea>
                    </div>
                    <div>
                        <label class="label text-xs">Image @if($item->image_path)<span class="text-gray-400 font-normal">(replace)</span>@endif</label>
                        @if($item->image_path)
                        <img src="{{ Storage::url($item->image_path) }}" class="w-full h-32 object-cover rounded-lg mb-2">
                        @endif
                        <input type="file" name="image" accept="image/*" class="input w-full">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-1">Save</button>
                        <button type="button" class="btn btn-outline btn-sm text-red-500 border-red-200"
                            onclick="if(confirm('Remove this exercise?')) { document.getElementById('del-{{ $item->id }}').submit(); }">Delete</button>
                    </div>
                </form>
                <form id="del-{{ $item->id }}" method="POST" action="{{ route('admin.briefing-items.destroy', $item) }}" style="display:none;">
                    @csrf @method('DELETE')
                </form>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-2">No exercises added yet.</p>
            @endforelse

            {{-- Add exercise --}}
            <div>
                <button type="button" @click="addOpen = !addOpen" class="btn btn-outline btn-sm w-full">
                    <span x-text="addOpen ? '− Cancel' : '+ Add Exercise'"></span>
                </button>
                <div x-show="addOpen" x-cloak id="add-form-{{ $week->id }}" class="mt-3 bg-brand/5 border border-brand/10 rounded-xl p-4">
                    <form method="POST" action="{{ route('admin.class-types.briefing.store', [$classType, $week]) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label text-xs">Exercise Name <span class="text-red-400">*</span></label>
                                <input type="text" name="exercise_name" class="input w-full" required placeholder="e.g. Hand Target">
                            </div>
                            <div>
                                <label class="label text-xs">Suggested Time</label>
                                <input type="text" name="suggested_time" class="input w-full" placeholder="e.g. 10 minutes">
                            </div>
                        </div>
                        <div>
                            <label class="label text-xs">Description</label>
                            <textarea name="description" rows="3" class="input w-full" placeholder="Instructions for the instructor..."></textarea>
                        </div>
                        <div>
                            <label class="label text-xs">Image (optional)</label>
                            <input type="file" name="image" accept="image/*" class="input w-full">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="_action" value="save" class="btn btn-primary btn-sm flex-1">Save</button>
                            <button type="submit" name="_action" value="save_and_add" class="btn btn-outline btn-sm flex-1">Save &amp; Add Another</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── TAB 4: GRADING ── --}}
@if($classType->has_grading)
<div x-show="tab==='grading'" class="space-y-6">
    <p class="text-sm text-gray-500">Set up the exercises used in the final exam. Choose marks-based (deduct for faults) or rating-based (score by rating scale).</p>

    {{-- Existing exercises --}}
    @forelse($classType->gradingExercises as $exercise)
    @php
        $badgeClass = match($exercise->type) {
            'marks'  => 'bg-blue-100 text-blue-700',
            'rating' => 'bg-purple-100 text-purple-700',
            'time'   => 'bg-green-100 text-green-700',
            default  => 'bg-gray-100 text-gray-700',
        };
        $typeLabel = match($exercise->type) {
            'marks'  => 'Marks-based',
            'rating' => 'Rating-based',
            'time'   => 'Time-based',
            default  => $exercise->type,
        };
    @endphp
    @php
        $autoOpenExercise = session('_open_exercise_id') == $exercise->id;
        $autoAddEvent     = $autoOpenExercise && session('_add_event_open');
        $autoAddRating    = $autoOpenExercise && session('_add_rating_open');
    @endphp
    <div class="card" id="exercise-{{ $exercise->id }}"
        x-data="{ open: {{ $autoOpenExercise ? 'true' : 'false' }}, addEvent: {{ $autoAddEvent ? 'true' : 'false' }}, addRating: {{ $autoAddRating ? 'true' : 'false' }} }"
        x-init="{{ $autoOpenExercise ? '$nextTick(() => { document.getElementById(\'exercise-bottom-' . $exercise->id . '\').scrollIntoView({ behavior: \'smooth\', block: \'end\' }) })' : '' }}">

        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3 cursor-pointer flex-1" @click="open = !open">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold {{ $badgeClass }}">
                    {{ $loop->iteration }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-navy text-sm">{{ $exercise->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $typeLabel }}
                        @if($exercise->type === 'marks' && $exercise->starting_marks)
                            · {{ $exercise->starting_marks }} marks
                            · {{ $exercise->deductionEvents->count() }} {{ Str::plural('event', $exercise->deductionEvents->count()) }}
                        @elseif($exercise->type === 'rating')
                            · {{ $exercise->ratingScales->count() }} ratings
                            @if($exercise->starting_marks) · {{ $exercise->starting_marks }} marks @endif
                        @elseif($exercise->type === 'time')
                            @if($exercise->target_time_seconds) · {{ $exercise->target_time_seconds }}s target @endif
                            @if($exercise->starting_marks) · {{ $exercise->starting_marks }} marks @endif
                            @if($exercise->allow_second_attempt) · 2nd chance @endif
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                {{-- Reorder buttons --}}
                @if(!$loop->first)
                <form method="POST" action="{{ route('admin.grading-exercises.reorder', $exercise) }}" style="display:inline" @click.stop>
                    @csrf
                    <input type="hidden" name="direction" value="up">
                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded text-gray-400 hover:text-navy hover:bg-gray-100 transition-colors" title="Move up">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M12 4v16"/></svg>
                    </button>
                </form>
                @endif
                @if(!$loop->last)
                <form method="POST" action="{{ route('admin.grading-exercises.reorder', $exercise) }}" style="display:inline" @click.stop>
                    @csrf
                    <input type="hidden" name="direction" value="down">
                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded text-gray-400 hover:text-navy hover:bg-gray-100 transition-colors" title="Move down">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7M12 20V4"/></svg>
                    </button>
                </form>
                @endif
                <svg class="w-4 h-4 text-gray-400 transition-transform cursor-pointer ml-1" :class="open ? 'rotate-180' : ''" @click="open = !open"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-gray-100 space-y-4">

            {{-- Edit exercise details --}}
            <form method="POST" action="{{ route('admin.grading-exercises.update', $exercise) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label text-xs">Exercise Name</label>
                    <input type="text" name="name" value="{{ $exercise->name }}" class="input w-full" required>
                </div>
                @if($exercise->type === 'marks')
                <div>
                    <label class="label text-xs">Starting Marks</label>
                    <input type="number" name="starting_marks" value="{{ $exercise->starting_marks }}" step="0.5" min="0" class="input w-40">
                </div>
                @elseif($exercise->type === 'rating')
                <div>
                    <label class="label text-xs">Total Marks for this Exercise</label>
                    <input type="number" name="starting_marks" value="{{ $exercise->starting_marks }}" step="0.5" min="0" class="input w-40" placeholder="e.g. 10">
                    <p class="text-xs text-gray-400 mt-1">Used to calculate the score as a percentage of the total.</p>
                </div>
                @elseif($exercise->type === 'time')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label text-xs">Target Time (seconds)</label>
                        <input type="number" name="target_time_seconds" value="{{ $exercise->target_time_seconds }}" min="1" class="input w-full" placeholder="e.g. 60">
                    </div>
                    <div>
                        <label class="label text-xs">Max Marks</label>
                        <input type="number" name="starting_marks" value="{{ $exercise->starting_marks }}" step="0.5" min="0" class="input w-full" placeholder="e.g. 10">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="allow_second_attempt" value="1" {{ $exercise->allow_second_attempt ? 'checked' : '' }} class="rounded text-brand">
                    Allow a 2nd attempt (higher of the two times counts)
                </label>
                @endif
                <div>
                    <label class="label text-xs">Description</label>
                    <textarea name="description" rows="2" class="input w-full">{{ $exercise->description }}</textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Save Exercise</button>
                    <button type="button" class="btn btn-outline btn-sm text-red-500 border-red-200"
                        onclick="if(confirm('Delete this exercise and all its events/ratings?')) document.getElementById('del-ex-{{ $exercise->id }}').submit()">Delete</button>
                </div>
            </form>
            <form id="del-ex-{{ $exercise->id }}" method="POST" action="{{ route('admin.grading-exercises.destroy', $exercise) }}" style="display:none">
                @csrf @method('DELETE')
            </form>

            @if($exercise->type === 'time')
            {{-- Time-based: no sub-items needed, settings are above --}}
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-xs text-green-700 font-medium">How it works: the actual time entered is divided by the target time to get a percentage, and that percentage of the max marks is awarded. If a 2nd attempt is allowed, the higher time counts.</p>
            </div>
            @elseif($exercise->type === 'marks')
            {{-- ── Deduction Events ── --}}
            <div class="bg-blue-50 rounded-xl p-4 space-y-3">
                <h4 class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Deduction Events</h4>

                @forelse($exercise->deductionEvents as $event)
                <form method="POST" action="{{ route('admin.grading-events.update', $event) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="event_name" value="{{ $event->event_name }}" class="input flex-1 text-sm" placeholder="Event name" required>
                    <div class="flex items-center gap-1 shrink-0">
                        <span class="text-xs text-gray-400">−</span>
                        <input type="number" name="marks_deducted" value="{{ $event->marks_deducted }}" step="0.5" min="0" class="input w-20 text-sm text-center">
                        <span class="text-xs text-gray-400">pts</span>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary shrink-0">Save</button>
                    <button type="button" class="btn btn-sm btn-outline text-red-500 border-red-200 shrink-0"
                        onclick="if(confirm('Remove?')) document.getElementById('del-ev-{{ $event->id }}').submit()">×</button>
                </form>
                {{-- Delete form lives OUTSIDE the update form --}}
                <form id="del-ev-{{ $event->id }}" method="POST" action="{{ route('admin.grading-events.destroy', $event) }}" style="display:none">@csrf @method('DELETE')</form>
                @empty
                <p class="text-xs text-gray-400">No events yet.</p>
                @endforelse

                {{-- Add event --}}
                <div x-show="!addEvent">
                    <button type="button" @click="addEvent = true" class="btn btn-outline btn-sm text-blue-700 border-blue-200 w-full">+ Add Event</button>
                </div>
                <div x-show="addEvent" x-cloak class="bg-white border border-blue-100 rounded-xl p-3 space-y-2">
                    <form method="POST" action="{{ route('admin.grading-exercises.events.store', $exercise) }}" class="space-y-2">
                        @csrf
                        <div class="flex items-center gap-2">
                            <input type="text" name="event_name" class="input flex-1 text-sm" placeholder="e.g. Dog sniffs ground" required>
                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-xs text-gray-400">−</span>
                                <input type="number" name="marks_deducted" step="0.5" min="0" class="input w-20 text-sm text-center" placeholder="0" required>
                                <span class="text-xs text-gray-400">pts</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="_action" value="save" class="btn btn-sm btn-primary flex-1">Save</button>
                            <button type="submit" name="_action" value="save_and_add" class="btn btn-sm btn-outline flex-1">Save &amp; Add Another</button>
                            <button type="button" @click="addEvent = false" class="btn btn-sm btn-outline">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            @elseif($exercise->type === 'rating')
            {{-- ── Rating Scale ── --}}
            <div class="bg-purple-50 rounded-xl p-4 space-y-3">
                <h4 class="text-xs font-semibold text-purple-700 uppercase tracking-wide">Rating Scale</h4>

                @forelse($exercise->ratingScales as $scale)
                {{-- Update form — no nested forms, delete button submits a separate form by ID --}}
                <form id="update-sc-{{ $scale->id }}" method="POST" action="{{ route('admin.grading-ratings.update', $scale) }}" class="bg-white border border-purple-100 rounded-xl p-3 space-y-2">
                    @csrf
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Rating</label>
                            <input type="text" name="label" value="{{ $scale->label }}" class="input w-full text-sm" placeholder="e.g. Excellent" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Points deducted</label>
                            <div class="flex items-center gap-1">
                                <span class="text-xs text-gray-400">−</span>
                                <input type="number" name="marks_deducted" value="{{ $scale->marks_deducted }}" step="0.5" min="0" class="input flex-1 text-sm text-center">
                                <span class="text-xs text-gray-400">pts</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Description</label>
                        <input type="text" name="description" value="{{ $scale->description }}" class="input w-full text-sm" placeholder="Describe what this rating means…">
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-1">
                        <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                            <input type="checkbox" name="is_automatic_fail" value="1" {{ $scale->is_automatic_fail ? 'checked' : '' }} class="rounded text-red-500">
                            Automatic fail
                        </label>
                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            <button type="button" class="btn btn-sm btn-outline text-red-500 border-red-200"
                                onclick="if(confirm('Remove this rating?')) document.getElementById('del-sc-{{ $scale->id }}').submit()">Remove</button>
                        </div>
                    </div>
                </form>
                {{-- Delete form lives OUTSIDE the update form --}}
                <form id="del-sc-{{ $scale->id }}" method="POST" action="{{ route('admin.grading-ratings.destroy', $scale) }}" style="display:none">@csrf @method('DELETE')</form>
                @empty
                <p class="text-xs text-gray-400">No ratings yet.</p>
                @endforelse

                {{-- Add rating --}}
                <div x-show="!addRating">
                    <button type="button" @click="addRating = true" class="btn btn-outline btn-sm text-purple-700 border-purple-200 w-full">+ Add Rating</button>
                </div>
                <div x-show="addRating" x-cloak class="bg-white border border-purple-100 rounded-xl p-3 space-y-3">
                    <form method="POST" action="{{ route('admin.grading-exercises.ratings.store', $exercise) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500 font-medium">Rating</label>
                                <input type="text" name="label" class="input w-full text-sm" placeholder="e.g. Excellent" required>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 font-medium">Points deducted</label>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-400">−</span>
                                    <input type="number" name="marks_deducted" step="0.5" min="0" class="input flex-1 text-sm text-center" placeholder="0" required>
                                    <span class="text-xs text-gray-400">pts</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Description</label>
                            <input type="text" name="description" class="input w-full text-sm" placeholder="Describe what this rating means…">
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" name="is_automatic_fail" value="1" class="rounded text-red-500">
                                Automatic fail
                            </label>
                            <div class="flex gap-2">
                                <button type="submit" name="_action" value="save" class="btn btn-sm btn-primary">Save</button>
                                <button type="submit" name="_action" value="save_and_add" class="btn btn-sm btn-outline">Save &amp; Add</button>
                                <button type="button" @click="addRating = false" class="btn btn-sm btn-outline">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div id="exercise-bottom-{{ $exercise->id }}"></div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <p class="text-gray-500 text-sm">No grading exercises set up yet.</p>
    </div>
    @endforelse

    {{-- Add new exercise --}}
    <div class="card" x-data="{ open: false, type: 'marks' }">
        <button type="button" @click="open = !open" class="btn btn-outline w-full">
            <span x-text="open ? '− Cancel' : '+ Add Grading Exercise'"></span>
        </button>

        <form x-show="open" x-cloak method="POST" action="{{ route('admin.class-types.grading.store', $classType) }}" class="mt-4 space-y-4">
            @csrf

            <div>
                <label class="label text-xs">Grading Type <span class="text-red-400">*</span></label>
                <div class="flex flex-wrap gap-4 mt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="marks" x-model="type" class="text-brand" checked>
                        <span class="text-sm text-gray-700 font-medium">Marks-based</span>
                        <span class="text-xs text-gray-400">(deduct marks per fault)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="rating" x-model="type" class="text-brand">
                        <span class="text-sm text-gray-700 font-medium">Rating-based</span>
                        <span class="text-xs text-gray-400">(score by rating scale)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="time" x-model="type" class="text-brand">
                        <span class="text-sm text-gray-700 font-medium">Time-based</span>
                        <span class="text-xs text-gray-400">(proportional to target time)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="label text-xs">Exercise Name <span class="text-red-400">*</span></label>
                <input type="text" name="name" class="input w-full" placeholder="e.g. Heelwork" required>
            </div>

            {{-- Marks-based: starting marks --}}
            <div x-show="type === 'marks'">
                <label class="label text-xs">Starting Marks</label>
                <input type="number" name="starting_marks" step="0.5" min="0" class="input w-40" placeholder="e.g. 10"
                    :disabled="type !== 'marks'">
            </div>

            {{-- Rating-based: total marks --}}
            <div x-show="type === 'rating'">
                <label class="label text-xs">Total Marks for this Exercise</label>
                <input type="number" name="starting_marks" step="0.5" min="0" class="input w-40" placeholder="e.g. 10"
                    :disabled="type !== 'rating'">
                <p class="text-xs text-gray-400 mt-1">Used to calculate the score as a percentage of the total.</p>
            </div>

            {{-- Time-based: target time + max marks + 2nd attempt --}}
            <div x-show="type === 'time'" class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label text-xs">Target Time (seconds)</label>
                        <input type="number" name="target_time_seconds" min="1" class="input w-full" placeholder="e.g. 60"
                            :disabled="type !== 'time'">
                    </div>
                    <div>
                        <label class="label text-xs">Max Marks</label>
                        <input type="number" name="starting_marks" step="0.5" min="0" class="input w-full" placeholder="e.g. 10"
                            :disabled="type !== 'time'">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="allow_second_attempt" value="1" class="rounded text-brand"
                        :disabled="type !== 'time'">
                    Allow a 2nd attempt (higher of the two times counts)
                </label>
            </div>

            <div>
                <label class="label text-xs">Description</label>
                <textarea name="description" rows="2" class="input w-full" placeholder="What the exercise involves..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Add Exercise</button>
        </form>
    </div>
</div>
@endif

{{-- ── TAB 5: INFO PAGE ── --}}
<div x-show="tab==='info_page'">
    @include('admin.class-types._info-page-tab')
</div>

</div>
@endsection
