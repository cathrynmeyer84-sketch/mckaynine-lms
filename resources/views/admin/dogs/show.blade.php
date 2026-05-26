<x-app-layout :title="$dog->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.handlers.show', $dog->handler) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $dog->name }}</h1>
                <p class="page-subtitle">
                    <a href="{{ route('admin.handlers.show', $dog->handler) }}" class="hover:text-brand">
                        {{ $dog->handler->first_name }} {{ $dog->handler->last_name }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Photo + basics --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card text-center">
                @if($dog->photo_path)
                    <img src="{{ Storage::url($dog->photo_path) }}" alt="{{ $dog->name }}"
                        class="w-32 h-32 rounded-2xl object-cover mx-auto mb-4">
                @else
                    <div class="w-32 h-32 rounded-2xl bg-stone/20 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-16 h-16 text-stone/60" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11H3V9H1.5v2H0v1.5h1.5V15H3v-2.5h1.5V11zm4.75-1.5A2.25 2.25 0 007 11.75v.5A2.25 2.25 0 009.25 14.5h.5A2.25 2.25 0 0012 12.25v-.5A2.25 2.25 0 009.75 9.5h-.5zm5.5 0A2.25 2.25 0 0012.5 11.75v.5A2.25 2.25 0 0014.75 14.5h.5A2.25 2.25 0 0017.5 12.25v-.5A2.25 2.25 0 0015.25 9.5h-.5zM22.5 11H21V9h-1.5v2H18v1.5h1.5V15H21v-2.5h1.5V11z"/></svg>
                    </div>
                @endif
                <h2 class="font-bold text-navy text-lg">{{ $dog->name }}</h2>
                @if($dog->breed)
                <p class="text-sm text-gray-500">{{ $dog->breed }}</p>
                @endif
                @if($dog->is_retired)
                <span class="badge mt-2">Retired</span>
                @endif
                @if($dog->multi_dog_discount)
                <span class="badge badge-active mt-1">Multi-dog discount</span>
                @endif
            </div>

            <div class="card space-y-3">
                <h2 class="font-semibold text-navy mb-1">Details</h2>

                @if($dog->date_of_birth)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Date of Birth</p>
                    <p class="text-sm text-gray-700">{{ $dog->date_of_birth->format('d M Y') }}
                        <span class="text-gray-400">({{ $dog->age }})</span>
                    </p>
                </div>
                @endif

                @if($dog->gender || $dog->gender_repro_status)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Gender / Reproductive Status</p>
                    <p class="text-sm text-gray-700">
                        {{ ucfirst($dog->gender ?? '') }}
                        @if($dog->gender_repro_status) — {{ $dog->gender_repro_status }}@endif
                    </p>
                </div>
                @endif

                @if($dog->microchip_number)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Microchip</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $dog->microchip_number }}</p>
                </div>
                @endif

                {{-- Vaccination --}}
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Vaccinations</p>
                    @if($dog->vaccination_expiry_date)
                        @php $expiry = $dog->vaccination_expiry_date; @endphp
                        @if($expiry->isPast())
                            <span class="text-xs text-red-600 font-medium bg-red-50 px-2 py-0.5 rounded-full">EXPIRED {{ $expiry->format('d M Y') }}</span>
                        @elseif($expiry->diffInDays() < 30)
                            <span class="text-xs text-amber font-medium bg-amber/10 px-2 py-0.5 rounded-full">Expires soon: {{ $expiry->format('d M Y') }}</span>
                        @else
                            <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Valid until {{ $expiry->format('d M Y') }}</span>
                        @endif
                    @else
                        <span class="text-xs text-gray-400">Not recorded</span>
                    @endif
                    @if($dog->vaccination_card_path)
                    <div class="mt-1">
                        <a href="{{ Storage::url($dog->vaccination_card_path) }}" target="_blank"
                            class="text-xs text-brand hover:underline flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            View vaccination card
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Multi-dog discount --}}
            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Multi-dog discount</p>
                        <p class="text-xs text-gray-400 mt-0.5">25% reduction applied when calculating instructor fees</p>
                    </div>
                    <form method="POST" action="{{ route('admin.dogs.multi-dog-discount', $dog) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $dog->multi_dog_discount ? 'bg-brand' : 'bg-gray-200' }}"
                            title="{{ $dog->multi_dog_discount ? 'Remove discount' : 'Enable discount' }}">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $dog->multi_dog_discount ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Right: Background + enrolments --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Background --}}
            @php
                $hasSocialisation = $dog->socialisation_other_dogs || $dog->socialisation_other_animals || $dog->socialisation_people || $dog->socialisation_sights_sounds || $dog->socialisation_details;
                $hasBackground = $dog->origin_story || $dog->age_when_acquired || $dog->animal_buddies_at_home || $dog->young_children_at_home;
            @endphp

            @if($hasBackground || $hasSocialisation || $dog->training_ambition || $dog->training_goal || $dog->has_behaviour_problems || $dog->has_health_issues)
            <div class="card space-y-4">
                <h2 class="font-semibold text-navy">Background & Training</h2>

                @if($dog->origin_story)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Origin Story</p>
                    <p class="text-sm text-gray-700">{{ $dog->origin_story }}</p>
                </div>
                @endif

                @if($dog->age_when_acquired)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Age When Acquired</p>
                    <p class="text-sm text-gray-700">{{ $dog->age_when_acquired }}</p>
                </div>
                @endif

                @if($dog->animal_buddies_at_home)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Animal Companions at Home</p>
                    @php $buddies = is_array($dog->animal_buddies_at_home) ? $dog->animal_buddies_at_home : [$dog->animal_buddies_at_home]; @endphp
                    <p class="text-sm text-gray-700">{{ implode(', ', $buddies) }}</p>
                </div>
                @endif

                @if($dog->young_children_at_home)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Young Children at Home</p>
                    @php $children = is_array($dog->young_children_at_home) ? $dog->young_children_at_home : [$dog->young_children_at_home]; @endphp
                    <p class="text-sm text-gray-700">{{ implode(', ', $children) }}</p>
                </div>
                @endif

                @if($hasSocialisation)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Socialisation</p>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach([
                            'Other dogs' => $dog->socialisation_other_dogs,
                            'Other animals' => $dog->socialisation_other_animals,
                            'People' => $dog->socialisation_people,
                            'Sights & sounds' => $dog->socialisation_sights_sounds,
                        ] as $label => $value)
                        @if($value)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @if($dog->socialisation_details)
                    <p class="text-sm text-gray-600 mt-2">{{ $dog->socialisation_details }}</p>
                    @endif
                </div>
                @endif

                @if($dog->training_ambition || $dog->training_goal)
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Training Goals</p>
                    @if($dog->training_ambition)<p class="text-sm text-gray-700">{{ $dog->training_ambition }}</p>@endif
                    @if($dog->training_goal)<p class="text-sm text-gray-700 mt-1">{{ $dog->training_goal }}</p>@endif
                </div>
                @endif

                @if($dog->has_behaviour_problems)
                <div class="bg-amber/10 border border-amber/30 rounded-xl px-4 py-3">
                    <p class="text-xs font-semibold text-amber uppercase tracking-wide mb-1">Behaviour Notes</p>
                    <p class="text-sm text-gray-700">{{ $dog->behaviour_problems_details ?: 'Flagged — no details provided.' }}</p>
                </div>
                @endif

                @if($dog->has_health_issues)
                <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3">
                    <p class="text-xs font-semibold text-red-500 uppercase tracking-wide mb-1">Health Notes</p>
                    <p class="text-sm text-gray-700">{{ $dog->health_issues ?: 'Flagged — no details provided.' }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Assign to Class --}}
            <div class="card" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-navy">Enrol in a Class</h2>
                    <button type="button" @click="open = !open" class="btn-outline btn-sm text-sm"
                        x-text="open ? 'Cancel' : '+ Assign Class'"></button>
                </div>
                <div x-show="open" x-cloak class="mt-4 pt-4 border-t border-gray-100">
                    @if($availableClasses->isEmpty())
                        <p class="text-sm text-gray-400">No available classes — this dog is already enrolled in all current classes, or there are no current classes.</p>
                    @else
                    <form method="POST" action="{{ route('admin.dogs.enrol', $dog) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select w-full" required>
                                <option value="">— Select a class —</option>
                                @foreach($availableClasses as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }}
                                    @if($class->start_date) · {{ $class->start_date->format('d M Y') }}@endif
                                    @if($class->classType) · {{ $class->classType->name }}@endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select w-full" required>
                                <option value="confirmed">Confirmed</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary w-full">Enrol {{ $dog->name }}</button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Enrolments --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Enrolments ({{ $dog->enrolments->count() }})</h2>
                @if($dog->enrolments->count())
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Status</th>
                                <th>Enrolled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dog->enrolments as $enrolment)
                            <tr>
                                <td class="font-medium text-gray-900">
                                    @if($enrolment->dogClass)
                                    <a href="{{ route('admin.classes.show', $enrolment->dogClass) }}" class="hover:text-brand">
                                        {{ $enrolment->dogClass->name }}
                                    </a>
                                    @else —
                                    @endif
                                </td>
                                <td>
                                    @php $sc = match($enrolment->status) { 'confirmed' => 'badge-confirmed', 'pending' => 'badge-pending', 'completed' => 'badge-completed', default => 'badge' }; @endphp
                                    <span class="badge {{ $sc }}">{{ ucfirst($enrolment->status) }}</span>
                                </td>
                                <td class="text-sm text-gray-500">{{ $enrolment->enrolled_at?->format('d M Y') ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400">No enrolments yet.</p>
                @endif
            </div>

            {{-- Class Results --}}
            @php $enrolmentsWithResults = $dog->enrolments->filter(fn($e) => $e->examResult); @endphp
            @if($enrolmentsWithResults->count())
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Class Results</h2>
                <div class="space-y-3">
                    @foreach($enrolmentsWithResults as $enrolment)
                    @php
                        $result = $enrolment->examResult;
                        $levelColour = match($result->achievement_level) {
                            'merit_pass'  => 'bg-amber/10 text-amber border-amber/30',
                            'pass'        => 'bg-green-50 text-green-700 border-green-200',
                            'completed'   => 'bg-green-50 text-green-700 border-green-200',
                            'review'      => 'bg-blue-50 text-blue-700 border-blue-200',
                            'fail'        => 'bg-red-50 text-red-600 border-red-200',
                            default       => 'bg-gray-50 text-gray-600 border-gray-200',
                        };
                        $levelLabel = match($result->achievement_level) {
                            'merit_pass' => 'Merit Pass',
                            'pass'       => 'Pass',
                            'completed'  => 'Completed',
                            'review'     => 'Under Review',
                            'fail'       => 'Fail',
                            default      => ucfirst($result->achievement_level ?? '—'),
                        };
                        $statusColour = match($result->status) {
                            'released'  => 'badge-confirmed',
                            'submitted' => 'badge-active',
                            default     => 'badge',
                        };
                    @endphp
                    <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <p class="font-medium text-gray-900 text-sm">
                                    @if($enrolment->dogClass)
                                        <a href="{{ route('admin.classes.show', $enrolment->dogClass) }}" class="hover:text-brand">
                                            {{ $enrolment->dogClass->name }}
                                        </a>
                                    @else —
                                    @endif
                                </p>
                                <span class="badge {{ $statusColour }} text-xs">{{ ucfirst($result->status) }}</span>
                            </div>
                            @if($result->exam_date)
                            <p class="text-xs text-gray-400">Exam date: {{ \Carbon\Carbon::parse($result->exam_date)->format('d M Y') }}</p>
                            @endif
                            @if($result->instructor_comments)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $result->instructor_comments }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right space-y-1">
                            @if($result->total_score !== null)
                            <p class="text-2xl font-bold text-navy">{{ number_format($result->total_score, 0) }}<span class="text-sm font-normal text-gray-400">%</span></p>
                            @endif
                            <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full border {{ $levelColour }}">{{ $levelLabel }}</span>
                            @if($result->status !== 'released')
                            <div class="mt-1">
                                <a href="{{ route('admin.results.show', $result) }}" class="text-xs text-brand hover:underline">Review →</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Assessment Form Responses --}}
            @foreach($dog->assessmentRequests as $assessment)
            <div class="card" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left">
                    <div>
                        <p class="font-semibold text-gray-900">Assessment Form</p>
                        <p class="text-xs text-gray-400">
                            Submitted {{ $assessment->created_at?->format('d M Y') }}
                            @if($assessment->slot) · {{ $assessment->slot->date->format('d M Y') }}@endif
                            ·
                            @php $asc = match($assessment->status) { 'booked' => 'badge-active', 'completed' => 'badge-confirmed', default => 'badge-pending' }; @endphp
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <span class="badge {{ $asc }}">{{ ucfirst($assessment->status) }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <div x-show="open" x-collapse class="mt-4 border-t border-gray-100 pt-4 space-y-4 text-sm">

                    @php
                        $fields = [
                            'Dog age description'           => $assessment->dog_age_description,
                            'Gender / reproductive status'  => $assessment->gender_repro_status,
                            'Where they got the dog'        => $assessment->where_got_dog,
                            'Age when acquired'             => $assessment->age_acquired,
                            'How long had dog'              => $assessment->how_long_had_dog,
                            'Health concerns'               => $assessment->health_concerns,
                            'Prior training'                => $assessment->prior_training,
                            'Comfort in busy environments'  => $assessment->comfort_in_busy_environments,
                        ];
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($fields as $label => $value)
                        @if($value)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mb-0.5">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach

                        @if($assessment->response_to_new_people)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mb-0.5">Response to new people (1–5)</p>
                            <p class="text-gray-700">{{ $assessment->response_to_new_people }}/5</p>
                        </div>
                        @endif
                    </div>

                    @if($assessment->training_goals)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Training Goals</p>
                        <p class="text-gray-700">{{ implode(', ', (array) $assessment->training_goals) }}</p>
                    </div>
                    @endif

                    @if($assessment->desired_outcomes)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Desired Outcomes</p>
                        <p class="text-gray-700 whitespace-pre-line">{{ $assessment->desired_outcomes }}</p>
                    </div>
                    @endif

                    @if($assessment->specific_issues)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Specific Issues</p>
                        <p class="text-gray-700 whitespace-pre-line">{{ $assessment->specific_issues }}</p>
                    </div>
                    @endif

                    @if($assessment->behaviour_around_dogs || $assessment->aggression_history)
                    <div class="bg-amber/10 border border-amber/20 rounded-xl px-4 py-3 space-y-2">
                        <p class="text-xs font-semibold text-amber uppercase tracking-wide">Behaviour / Aggression</p>
                        @if($assessment->behaviour_around_dogs)
                        <p class="text-gray-700"><span class="text-gray-400 text-xs">Around dogs: </span>{{ $assessment->behaviour_around_dogs }}</p>
                        @endif
                        @if($assessment->aggression_history)
                        <p class="text-gray-700"><span class="text-gray-400 text-xs">Aggression history: </span>{{ $assessment->aggression_history }}</p>
                        @endif
                        @if($assessment->aggression_targets)
                        <p class="text-gray-700"><span class="text-gray-400 text-xs">Targets: </span>{{ implode(', ', (array) $assessment->aggression_targets) }}</p>
                        @endif
                        @if($assessment->aggression_details)
                        <p class="text-gray-700 whitespace-pre-line">{{ $assessment->aggression_details }}</p>
                        @endif
                    </div>
                    @endif

                    @if($assessment->additional_notes)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Additional Notes</p>
                        <p class="text-gray-700 whitespace-pre-line">{{ $assessment->additional_notes }}</p>
                    </div>
                    @endif

                </div>
            </div>
            @endforeach

            {{-- Enrolment Form Details --}}
            @foreach($dog->enrolments as $enrolment)
            @if($enrolment->pathway || $enrolment->class_type_requested || $enrolment->admin_notes || $enrolment->goals->count())
            <div class="card" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left">
                    <div>
                        <p class="font-semibold text-gray-900">Enrolment Form — {{ $enrolment->dogClass?->name ?? 'Unknown class' }}</p>
                        <p class="text-xs text-gray-400">Enrolled {{ $enrolment->enrolled_at?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @php $sc = match($enrolment->status) { 'confirmed' => 'badge-confirmed', 'pending' => 'badge-pending', 'completed' => 'badge-completed', default => 'badge' }; @endphp
                        <span class="badge {{ $sc }}">{{ ucfirst($enrolment->status) }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>

                <div x-show="open" x-collapse class="mt-4 border-t border-gray-100 pt-4 space-y-4 text-sm">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if($enrolment->pathway)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mb-0.5">Pathway</p>
                            <p class="text-gray-700">{{ ucfirst($enrolment->pathway) }}</p>
                        </div>
                        @endif
                        @if($enrolment->class_type_requested)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mb-0.5">Class type requested</p>
                            <p class="text-gray-700">{{ $enrolment->class_type_requested }}</p>
                        </div>
                        @endif
                        @if($enrolment->branch)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mb-0.5">Branch</p>
                            <p class="text-gray-700">{{ $enrolment->branch }}</p>
                        </div>
                        @endif
                        @if($enrolment->invoice_reference)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400 mb-0.5">Invoice reference</p>
                            <p class="text-gray-700 font-mono">{{ $enrolment->invoice_reference }}</p>
                        </div>
                        @endif
                    </div>

                    @if($enrolment->admin_notes)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Admin Notes</p>
                        <p class="text-gray-700 whitespace-pre-line">{{ $enrolment->admin_notes }}</p>
                    </div>
                    @endif

                    @if($enrolment->goals->count())
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Goals</p>
                        <div class="space-y-2">
                            @foreach($enrolment->goals as $goal)
                            <div class="bg-gray-50 rounded-lg px-3 py-2">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-gray-800 font-medium">{{ $goal->goal }}</p>
                                    @php $gs = match($goal->status) { 'achieved' => 'badge-confirmed', 'in_progress' => 'badge-active', default => 'badge' }; @endphp
                                    <span class="badge badge-sm {{ $gs }}">{{ ucfirst(str_replace('_', ' ', $goal->status)) }}</span>
                                </div>
                                @if($goal->progress_notes)
                                <p class="text-xs text-gray-500">{{ $goal->progress_notes }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif
            @endforeach

        </div>
    </div>

</div>
</x-app-layout>
