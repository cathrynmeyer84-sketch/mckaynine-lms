<x-app-layout :title="'Assessment: ' . $assessmentRequest->handler?->first_name . ' ' . $assessmentRequest->handler?->last_name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.assessments.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Assessment Request</h1>
                <p class="page-subtitle">{{ $assessmentRequest->handler?->first_name }} {{ $assessmentRequest->handler?->last_name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(in_array($assessmentRequest->status, ['pending', 'reviewed']))
            <form method="POST" action="{{ route('admin.assessments.send-booking-link', $assessmentRequest) }}">
                @csrf
                <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Send booking link to {{ $assessmentRequest->handler?->user?->email }}?')">
                    Send Booking Link
                </button>
            </form>
            @endif
            @if(in_array($assessmentRequest->status, ['booked']))
            <a href="{{ route('admin.assessments.score', $assessmentRequest) }}" class="btn btn-primary">
                Score Assessment
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column --}}
        <div class="space-y-6">

            {{-- Handler Info --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Handler</h2>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Name</p>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $assessmentRequest->handler?->first_name }} {{ $assessmentRequest->handler?->last_name }}
                        </p>
                    </div>
                    @if($assessmentRequest->handler?->user)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                        <p class="text-sm text-gray-700">{{ $assessmentRequest->handler->user->email }}</p>
                    </div>
                    @endif
                    @if($assessmentRequest->handler?->cell_number)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                        <p class="text-sm text-gray-700">{{ $assessmentRequest->handler->cell_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Dog Info --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Dog</h2>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $assessmentRequest->dog?->name }}</p>
                    </div>
                    @if($assessmentRequest->dog?->breed)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Breed</p>
                        <p class="text-sm text-gray-700">{{ $assessmentRequest->dog->breed }}</p>
                    </div>
                    @endif
                    @if($assessmentRequest->dog?->date_of_birth)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Date of Birth</p>
                        <p class="text-sm text-gray-700">{{ $assessmentRequest->dog->date_of_birth->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($assessmentRequest->dog?->sex)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Sex</p>
                        <p class="text-sm text-gray-700">{{ ucfirst($assessmentRequest->dog->sex) }}</p>
                    </div>
                    @endif
                    @if($assessmentRequest->dog?->neutered !== null)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Neutered/Spayed</p>
                        <p class="text-sm text-gray-700">{{ $assessmentRequest->dog->neutered ? 'Yes' : 'No' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vaccination Card --}}
            @if($assessmentRequest->dog?->vaccination_card_path)
            <div class="card">
                <h2 class="font-semibold text-navy mb-3">Vaccination Card</h2>
                @php
                    $vacPath = $assessmentRequest->dog->vaccination_card_path;
                    $vacUrl  = \Illuminate\Support\Facades\Storage::url($vacPath);
                    $isPdf   = str_ends_with(strtolower($vacPath), '.pdf');
                @endphp
                @if($isPdf)
                    <a href="{{ $vacUrl }}" target="_blank" class="flex items-center gap-2 text-brand hover:underline text-sm font-medium">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        View Vaccination Card (PDF)
                    </a>
                @else
                    <a href="{{ $vacUrl }}" target="_blank">
                        <img src="{{ $vacUrl }}" alt="Vaccination card" class="rounded-xl border border-gray-200 max-h-64 w-full object-contain bg-gray-50">
                    </a>
                @endif
                @if($assessmentRequest->dog?->vaccination_expiry_date)
                <p class="text-xs text-gray-400 mt-2">Expires: {{ \Carbon\Carbon::parse($assessmentRequest->dog->vaccination_expiry_date)->format('d M Y') }}</p>
                @endif
            </div>
            @endif

            {{-- Slot Info --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Assessment Slot</h2>
                @if($assessmentRequest->slot)
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Date</p>
                        <p class="text-sm text-gray-900 font-medium">{{ \Carbon\Carbon::parse($assessmentRequest->slot->date)->format('l, d M Y') }}</p>
                    </div>
                    @if($assessmentRequest->slot->start_time)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Time</p>
                        <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($assessmentRequest->slot->start_time)->format('g:i A') }}</p>
                    </div>
                    @endif
                    @if($assessmentRequest->slot->notes)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Notes</p>
                        <p class="text-sm text-gray-700">{{ $assessmentRequest->slot->notes }}</p>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-sm text-gray-500">No slot booked yet.</p>
                @endif
            </div>

        </div>

        {{-- Right column: Intake form answers --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-navy">Assessment Status</h2>
                    @php
                        $asc = match($assessmentRequest->status) {
                            'pending'      => 'badge-pending',
                            'slot_offered' => 'badge badge-amber',
                            'booked'       => 'badge-active',
                            'completed'    => 'badge-completed',
                            default        => 'badge'
                        };
                        $statusLabel = match($assessmentRequest->status) {
                            'slot_offered' => 'Booking Link Sent',
                            default        => ucfirst(str_replace('_', ' ', $assessmentRequest->status)),
                        };
                    @endphp
                    <span class="badge {{ $asc }}">{{ $statusLabel }}</span>
                </div>
                <p class="text-sm text-gray-500">Submitted {{ $assessmentRequest->created_at?->format('d M Y \a\t g:i A') }}</p>
            </div>

            {{-- Intake form data --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Intake Form Responses</h2>
                @php
                $formFields = [
                    'dog_age_description'        => 'How old is the dog?',
                    'how_long_had_dog'           => 'How long have you had the dog?',
                    'gender_repro_status'        => 'Gender / Reproductive status',
                    'where_got_dog'              => 'Where did you get your dog?',
                    'age_acquired'               => 'Age when acquired',
                    'health_concerns'            => 'Health concerns',
                    'training_goals'             => 'Training goals',
                    'desired_outcomes'           => 'Desired outcomes',
                    'specific_issues'            => 'Specific issues or concerns',
                    'response_to_new_people'     => 'Response to new people (1–5)',
                    'behaviour_around_dogs'      => 'Behaviour around other dogs',
                    'aggression_history'         => 'History of aggression',
                    'aggression_targets'         => 'Aggression directed at',
                    'aggression_details'         => 'Aggression details',
                    'prior_training'             => 'Prior formal training',
                    'comfort_in_busy_environments' => 'Comfort in busy environments',
                    'comfortable_with_assessment'  => 'Comfortable with assessment process',
                    'open_to_recommendation'     => 'Open to recommendation',
                    'additional_notes'           => 'Anything else',
                ];
                @endphp
                <div class="space-y-4">
                    @foreach($formFields as $field => $label)
                    @php $val = $assessmentRequest->$field ?? null; @endphp
                    <div class="pb-3 border-b border-gray-100 last:border-0">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $label }}</p>
                        <p class="text-sm text-gray-900">
                            @if(is_null($val) || $val === '' || $val === [])
                                <span class="text-gray-400">—</span>
                            @elseif(is_array($val))
                                {{ implode(', ', array_filter($val)) ?: '—' }}
                            @elseif(is_bool($val))
                                {{ $val ? 'Yes' : 'No' }}
                            @else
                                {{ $val }}
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Score Result --}}
            @if($assessmentRequest->scores)
            @php $score = $assessmentRequest->scores; @endphp
            <div class="card border-2 {{ $score->final_outcome === 'group_class' ? 'border-green-200' : ($score->final_outcome === 'private_lessons' ? 'border-amber' : 'border-red-200') }}">
                <h2 class="font-semibold text-navy mb-4">Assessment Result</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    @foreach(['step1_score','step2_score','step3_score','step4_score','step5_score','step6_score','step7_score'] as $i => $step)
                    @if(!($step === 'step7_score' && $score->step7_skipped))
                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500">Step {{ $i + 1 }}</p>
                        <p class="text-2xl font-bold text-navy">{{ $score->$step ?? '—' }}</p>
                    </div>
                    @endif
                    @endforeach
                </div>
                @if($score->step7_skipped)
                <p class="text-sm text-amber mb-3">Step 7 skipped: {{ $score->step7_skip_reason }}</p>
                @endif
                <div class="p-4 rounded-xl {{ $score->final_outcome === 'group_class' ? 'bg-green-50' : ($score->final_outcome === 'private_lessons' ? 'bg-amber/10' : 'bg-red-50') }}">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Final Outcome</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ ucwords(str_replace('_', ' ', $score->final_outcome)) }}</p>
                    @if($score->override_reason)
                    <p class="text-sm text-gray-600 mt-1">Override reason: {{ $score->override_reason }}</p>
                    @endif
                </div>
                @if($score->global_notes)
                <div class="mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Evaluator Notes</p>
                    <p class="text-sm text-gray-700">{{ $score->global_notes }}</p>
                </div>
                @endif
                <div class="mt-4 flex gap-2">
                    @if($score->status !== 'outcome_sent')
                    <form method="POST" action="{{ route('admin.assessments.release', $score) }}">
                        @csrf
                        <button type="submit" class="btn-amber">Release Outcome to Handler</button>
                    </form>
                    @else
                    <span class="badge badge-completed">Outcome sent to handler</span>
                    @endif
                    <a href="{{ route('admin.assessments.score', $assessmentRequest) }}" class="btn-outline">Edit Score</a>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</x-app-layout>
