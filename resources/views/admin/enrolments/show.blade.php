<x-app-layout :title="'Enrolment — ' . $enrolment->dog?->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.enrolments.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Enrolment — {{ $enrolment->dog?->name }}</h1>
                <p class="page-subtitle">{{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }} · <span class="badge {{ $enrolment->status_badge_class }}">{{ $enrolment->status_label }}</span></p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.handlers.show', $enrolment->handler) }}" class="btn-outline btn-sm">View Handler</a>
            <a href="{{ route('admin.dogs.show', $enrolment->dog) }}" class="btn-outline btn-sm">View Dog</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error mb-6">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Actions --}}
        <div class="space-y-4">

            {{-- Current Status --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-3">Status</h2>
                <span class="badge {{ $enrolment->status_badge_class }} text-sm">{{ $enrolment->status_label }}</span>
                @if($enrolment->enrolled_at)
                <p class="text-xs text-gray-400 mt-2">Enrolled {{ $enrolment->enrolled_at->format('d M Y') }}</p>
                @endif
                @if($enrolment->confirmed_at)
                <p class="text-xs text-gray-400">Confirmed {{ $enrolment->confirmed_at->format('d M Y') }}</p>
                @endif
                @if($enrolment->vet_clearance_requested_at)
                <p class="text-xs text-gray-400">Vet clearance requested {{ $enrolment->vet_clearance_requested_at->format('d M Y') }}</p>
                @endif
            </div>

            {{-- Vet Clearance Status --}}
            @if($enrolment->vet_clearance_path)
            <div class="card bg-green-50 border border-green-100">
                <p class="text-sm font-semibold text-green-700 mb-1">Vet Clearance Uploaded</p>
                <a href="{{ Storage::url($enrolment->vet_clearance_path) }}" target="_blank"
                    class="text-xs text-brand underline">Download certificate →</a>
            </div>
            @endif

            @if($enrolment->pathway === 'existing')
            {{-- Existing handler re-enrolment: just confirm the class --}}
            @if($enrolment->status === 'pending')
            <div class="card space-y-2">
                <h2 class="font-semibold text-navy">Confirm Class</h2>
                <p class="text-xs text-gray-500">Sends the class confirmation email and inbox message to {{ $enrolment->handler?->first_name }}.</p>
                @if($enrolment->dogClass)
                <p class="text-sm text-gray-700 font-medium">{{ $enrolment->dogClass->name }}</p>
                @endif
                <form method="POST" action="{{ route('admin.enrolments.confirm-class', $enrolment) }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full"
                        onclick="return confirm('Confirm {{ $enrolment->dog?->name }} in {{ $enrolment->dogClass?->name }}?')">
                        ✓ Confirm Class
                    </button>
                </form>
            </div>
            @endif

            @else
            {{-- New enrolment: two-step confirm + assign --}}

            {{-- Step 1: Confirm Enrolment --}}
            @if(in_array($enrolment->status, ['pending', 'vet_clearance_review']))
            <div class="card space-y-3">
                <p class="text-xs font-semibold text-brand uppercase tracking-wide">Step 1</p>
                <h2 class="font-semibold text-navy">Confirm Enrolment</h2>
                <p class="text-xs text-gray-500">Sends the welcome email with account setup link.</p>
                <form method="POST" action="{{ route('admin.enrolments.confirm', $enrolment) }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full"
                        onclick="return confirm('Confirm this enrolment and send the welcome email?')">
                        ✓ Confirm Enrolment
                    </button>
                </form>
                @if($enrolment->status === 'pending')
                <form method="POST" action="{{ route('admin.enrolments.request-vet-clearance', $enrolment) }}">
                    @csrf
                    <button type="submit" class="btn-outline w-full"
                        onclick="return confirm('Send vet clearance request email to {{ $enrolment->handler?->first_name }}?')">
                        Request Vet Clearance
                    </button>
                </form>
                @endif
            </div>
            @endif

            {{-- Step 2: Assign class --}}
            @if($enrolment->status === 'pending_class_assignment')
            <div class="card space-y-2">
                <p class="text-xs font-semibold text-brand uppercase tracking-wide">Step 2</p>
                <h2 class="font-semibold text-navy">Assign to Class</h2>
                <p class="text-xs text-gray-500">Sends the class confirmation email and inbox message with class dates.</p>
                <form method="POST" action="{{ route('admin.enrolments.assign-class', $enrolment) }}" class="space-y-2">
                    @csrf
                    <select name="class_id" class="form-select w-full" required>
                        <option value="">— select class —</option>
                        @foreach($availableClasses as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary w-full">Confirm Class</button>
                </form>
            </div>
            @endif

            @endif

            {{-- Reject --}}
            @if(!in_array($enrolment->status, ['confirmed', 'completed', 'withdrawn']))
            <div class="card border-red-100" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="text-sm text-red-500 font-medium hover:text-red-700 w-full text-left">
                    Reject Enrolment
                </button>
                <div x-show="open" x-collapse class="mt-3">
                    <form method="POST" action="{{ route('admin.enrolments.reject', $enrolment) }}" class="space-y-2">
                        @csrf
                        <label class="form-label">Reason for rejection</label>
                        <textarea name="rejection_reason" rows="3" class="form-textarea w-full"
                            placeholder="Please provide a brief reason..." required></textarea>
                        <button type="submit" class="btn-outline w-full text-red-500 border-red-200 hover:bg-red-50"
                            onclick="return confirm('Reject this enrolment and send notification?')">
                            Confirm Rejection
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>

        {{-- Right: Form details --}}
        <div class="lg:col-span-2 space-y-4">

            @php $dog = $enrolment->dog; $handler = $enrolment->handler; @endphp

            {{-- Dog + Handler summary --}}
            <div class="card">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <h2 class="font-semibold text-navy mb-3">Dog</h2>
                        <div class="flex items-center gap-3 mb-3">
                            @if($dog?->photo_path)
                            <img src="{{ Storage::url($dog->photo_path) }}" class="w-12 h-12 rounded-xl object-cover">
                            @else
                            <div class="w-12 h-12 rounded-xl bg-stone/20 flex items-center justify-center">
                                <svg class="w-6 h-6 text-stone/50" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11H3V9H1.5v2H0v1.5h1.5V15H3v-2.5h1.5V11z"/></svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $dog?->name }}</p>
                                <p class="text-xs text-gray-500">{{ $dog?->breed ?? 'Breed unknown' }}</p>
                            </div>
                        </div>
                        @if($dog?->date_of_birth)
                        <p class="text-xs text-gray-500">Age: {{ $dog->age }}</p>
                        @endif
                    </div>
                    <div>
                        <h2 class="font-semibold text-navy mb-3">Handler</h2>
                        <p class="text-sm font-medium text-gray-900">{{ $handler?->first_name }} {{ $handler?->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $handler?->user?->email }}</p>
                        @if($handler?->cell_number)
                        <p class="text-xs text-gray-500">{{ $handler->cell_number }}</p>
                        @endif
                        @if($handler?->occupation)
                        <p class="text-xs text-gray-500">{{ $handler->occupation }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vaccination --}}
            <div class="card" x-data="{ open: true }">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left">
                    <h2 class="font-semibold text-navy">Vaccination</h2>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    <div class="mt-4 space-y-3">
                        @if($dog?->vaccination_expiry_date)
                        @php $expiry = $dog->vaccination_expiry_date; $expired = $expiry->isPast(); @endphp
                        <div class="flex items-center gap-3 px-3 py-2 rounded-lg {{ $expired ? 'bg-red-50 border border-red-100' : 'bg-green-50 border border-green-100' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ $expired ? 'text-red-500' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <div>
                                <p class="text-xs font-semibold {{ $expired ? 'text-red-600' : 'text-green-700' }}">{{ $expired ? 'Vaccinations EXPIRED' : 'Vaccinations valid' }}</p>
                                <p class="text-xs {{ $expired ? 'text-red-500' : 'text-green-600' }}">Expiry: {{ $expiry->format('d M Y') }}</p>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">No expiry date recorded.</p>
                        @endif

                        @if($dog?->vaccination_card_path)
                        @php
                            $cardUrl = Storage::url($dog->vaccination_card_path);
                            $ext = strtolower(pathinfo($dog->vaccination_card_path, PATHINFO_EXTENSION));
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                        @endphp
                        <div>
                            <p class="text-xs text-gray-400 mb-2">Vaccination card</p>
                            @if($isImage)
                            <a href="{{ $cardUrl }}" target="_blank">
                                <img src="{{ $cardUrl }}" alt="Vaccination card"
                                    class="w-full max-w-sm rounded-lg border border-gray-200 object-contain hover:opacity-90 transition-opacity">
                            </a>
                            @else
                            <a href="{{ $cardUrl }}" target="_blank"
                                class="inline-flex items-center gap-2 text-sm text-brand underline hover:text-navy">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download vaccination card ({{ strtoupper($ext) }})
                            </a>
                            @endif
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">No vaccination card uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Enrolment form details --}}
            <div class="card" x-data="{ open: true }">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left mb-1">
                    <h2 class="font-semibold text-navy">Enrolment Form</h2>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-sm">

                        {{-- Handler details --}}
                        @foreach([
                            'Phone'          => $handler?->cell_number,
                            'Occupation'     => $handler?->occupation,
                            'Vet / Practice' => $handler?->vet_name_location,
                        ] as $label => $value)
                        @if(!is_null($value) && $value !== '')
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach

                        {{-- How did you hear --}}
                        @if($handler?->hear_about_us_sources)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">How did you hear about us</p>
                            <p class="text-gray-700">{{ is_array($handler->hear_about_us_sources) ? implode(', ', $handler->hear_about_us_sources) : $handler->hear_about_us_sources }}</p>
                        </div>
                        @endif

                        {{-- Account holder --}}
                        @if($handler?->accountHolder)
                        @php $ah = $handler->accountHolder; @endphp
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">Account holder (different from handler)</p>
                            <p class="text-gray-700">{{ $ah->name }}@if($ah->email) · {{ $ah->email }}@endif</p>
                        </div>
                        @endif

                        {{-- Dog details --}}
                        @foreach([
                            'Gender'            => $dog?->gender ? ucfirst($dog->gender) : null,
                            'Spay / neuter'     => $dog?->spay_neuter_status,
                            'Microchip number'  => $dog?->microchip_number,
                        ] as $label => $value)
                        @if(!is_null($value) && $value !== '')
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach

                        {{-- Training goal --}}
                        @if($dog?->training_goal)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">Training goal</p>
                            <p class="text-gray-700">{{ $dog->training_goal === 'competitive_dog_sport' ? 'Competitive dog sport' : 'Chilled canine companion' }}</p>
                        </div>
                        @endif

                        {{-- Other pets & children --}}
                        @if($dog?->animal_buddies_at_home)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">Other pets at home</p>
                            <p class="text-gray-700">{{ is_array($dog->animal_buddies_at_home) ? implode(', ', $dog->animal_buddies_at_home) : $dog->animal_buddies_at_home }}</p>
                        </div>
                        @endif
                        @if($dog?->young_children_at_home)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">Children at home</p>
                            <p class="text-gray-700">{{ is_array($dog->young_children_at_home) ? implode(', ', $dog->young_children_at_home) : $dog->young_children_at_home }}</p>
                        </div>
                        @endif

                        {{-- Socialisation --}}
                        @foreach([
                            'Socialisation — other dogs'    => $dog?->socialisation_other_dogs,
                            'Socialisation — other animals' => $dog?->socialisation_other_animals,
                            'Socialisation — people'        => $dog?->socialisation_people,
                        ] as $label => $value)
                        @if(!is_null($value) && $value !== '')
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach

                        @if($dog?->origin_story)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">Where did you get {{ $dog->name }}?</p>
                            <p class="text-gray-700">{{ $dog->origin_story }}</p>
                        </div>
                        @endif

                        @if($dog?->age_when_acquired)
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">Age when acquired</p>
                            <p class="text-gray-700">{{ $dog->age_when_acquired }}</p>
                        </div>
                        @endif

                        {{-- Consents --}}
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">WhatsApp consent</p>
                            <p class="text-gray-700">{{ $handler?->whatsapp_consent ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">Photo / social media consent</p>
                            <p class="text-gray-700">{{ $handler?->photo_consent ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">Ground rules agreed</p>
                            <p class="text-gray-700">{{ $handler?->ground_rules_agreed ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">Terms agreed</p>
                            <p class="text-gray-700">{{ $handler?->terms_agreed ? 'Yes' : 'No' }}</p>
                        </div>

                        {{-- Enrolment meta --}}
                        @foreach([
                            'Class assigned'  => $enrolment->dogClass?->name ?? 'Not yet assigned',
                            'Pathway'         => ucfirst($enrolment->pathway ?? ''),
                            'Branch'          => $enrolment->branch,
                        ] as $label => $value)
                        @if(!is_null($value) && $value !== '')
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach

                    </div>

                    {{-- Behaviour concerns --}}
                    @if($dog?->behaviour_problems_details)
                    <div class="mt-3 bg-amber/10 border border-amber/20 rounded-lg px-3 py-2">
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1">Behaviour concerns</p>
                        <p class="text-sm text-gray-700">{{ $dog->behaviour_problems_details }}</p>
                    </div>
                    @endif

                    {{-- Health concerns --}}
                    @if($dog?->health_issues)
                    <div class="mt-3 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-1">Health concerns</p>
                        <p class="text-sm text-gray-700">{{ $dog->health_issues }}</p>
                    </div>
                    @endif

                    @if($enrolment->admin_notes)
                    <div class="mt-3 bg-amber/10 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-400 mb-1">Admin notes</p>
                        <p class="text-sm text-gray-700">{{ $enrolment->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Assessment form --}}
            @php $assessment = $dog?->assessmentRequests->sortByDesc('created_at')->first(); @endphp
            @if($assessment)
            <div class="card" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left">
                    <h2 class="font-semibold text-navy">Assessment Form</h2>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-collapse>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-sm">
                        @foreach([
                            'Dog age (at time of assessment)' => $assessment->dog_age_description,
                            'Where they got the dog'          => $assessment->where_got_dog,
                            'Age when acquired'               => $assessment->age_acquired,
                            'How long had the dog'            => $assessment->how_long_had_dog,
                            'Prior training'                  => $assessment->prior_training,
                            'Response to new people (1–5)'   => $assessment->response_to_new_people,
                            'Behaviour around dogs'           => $assessment->behaviour_around_dogs,
                            'Comfortable in busy environments'=> $assessment->comfort_in_busy_environments,
                            'Comfortable with assessment'     => $assessment->comfortable_with_assessment,
                            'Open to recommendation'         => $assessment->open_to_recommendation,
                        ] as $label => $value)
                        @if(!is_null($value) && $value !== '')
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                            <p class="text-gray-700">{{ $value }}</p>
                        </div>
                        @endif
                        @endforeach

                        @if($assessment->training_goals)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">Training goals</p>
                            <p class="text-gray-700">{{ is_array($assessment->training_goals) ? implode(', ', $assessment->training_goals) : $assessment->training_goals }}</p>
                        </div>
                        @endif

                        @if($assessment->desired_outcomes)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">Desired outcomes</p>
                            <p class="text-gray-700">{{ $assessment->desired_outcomes }}</p>
                        </div>
                        @endif

                        @if($assessment->specific_issues)
                        <div class="bg-gray-50 rounded-lg px-3 py-2 sm:col-span-2">
                            <p class="text-xs text-gray-400">Specific issues</p>
                            <p class="text-gray-700">{{ $assessment->specific_issues }}</p>
                        </div>
                        @endif
                    </div>

                    @if($assessment->health_concerns)
                    <div class="mt-3 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide mb-1">Health concerns</p>
                        <p class="text-sm text-gray-700">{{ $assessment->health_concerns }}</p>
                    </div>
                    @endif

                    @if($assessment->aggression_history)
                    <div class="mt-3 bg-amber/10 border border-amber/20 rounded-lg px-3 py-2">
                        <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-1">Aggression history</p>
                        @if($assessment->aggression_targets)
                        <p class="text-xs text-gray-500 mb-1">Targets: {{ is_array($assessment->aggression_targets) ? implode(', ', $assessment->aggression_targets) : $assessment->aggression_targets }}</p>
                        @endif
                        @if($assessment->aggression_details)
                        <p class="text-sm text-gray-700">{{ $assessment->aggression_details }}</p>
                        @endif
                    </div>
                    @endif

                    @if($assessment->additional_notes)
                    <div class="mt-3 bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-400 mb-1">Additional notes</p>
                        <p class="text-sm text-gray-700">{{ $assessment->additional_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</x-app-layout>
