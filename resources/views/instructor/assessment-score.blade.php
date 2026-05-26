@extends('layouts.app')

@section('title', 'Assessment Score Sheet')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Assessment Score Sheet</h1>
        <p class="page-subtitle">{{ $assessmentRequest->dog->name }} &middot; {{ $assessmentRequest->handler->full_name }}</p>
    </div>
    <a href="{{ route('admin.assessments.show', $assessmentRequest) }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content max-w-2xl"
    x-data="{
        scores: {
            1: {{ $existing?->step1_score ?? 0 }},
            2: {{ $existing?->step2_score ?? 0 }},
            3: {{ $existing?->step3_score ?? 0 }},
            4: {{ $existing?->step4_score ?? 0 }},
            5: {{ $existing?->step5_score ?? 0 }},
            6: {{ $existing?->step6_score ?? 0 }},
            7: {{ $existing?->step7_score ?? 0 }}
        },
        step7Skipped: {{ $existing?->step7_skipped ? 'true' : 'false' }},
        get avg() {
            let vals = [this.scores[1], this.scores[2], this.scores[3], this.scores[4], this.scores[5], this.scores[6]];
            if (!this.step7Skipped && this.scores[7]) vals.push(this.scores[7]);
            let filled = vals.filter(v => v > 0);
            if (!filled.length) return 0;
            return filled.reduce((a,b) => a+b, 0) / filled.length;
        },
        get recommendation() {
            if (this.avg === 0) return '—';
            if (this.avg <= 2) return 'Group Class';
            if (this.avg <= 3.5) return 'Private Lessons';
            return 'Behaviourist Referral';
        },
        get recommendationColor() {
            if (this.avg === 0) return 'text-gray-400';
            if (this.avg <= 2) return 'text-green-600';
            if (this.avg <= 3.5) return 'text-amber';
            return 'text-red-600';
        }
    }">

    {{-- Handler & Dog Info --}}
    <div class="card mb-6">
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Handler</p>
                <p class="font-medium text-navy">{{ $assessmentRequest->handler->full_name }}</p>
            </div>
            <div>
                <p class="text-gray-500">Dog</p>
                <p class="font-medium text-navy">{{ $assessmentRequest->dog->name }}</p>
            </div>
            @if($assessmentRequest->dog->breed)
            <div>
                <p class="text-gray-500">Breed</p>
                <p class="font-medium text-navy">{{ $assessmentRequest->dog->breed }}</p>
            </div>
            @endif
            @if($assessmentRequest->dog->age_in_months)
            <div>
                <p class="text-gray-500">Age</p>
                <p class="font-medium text-navy">{{ $assessmentRequest->dog->age_in_months }} months</p>
            </div>
            @endif
        </div>
        @if($assessmentRequest->aggression_history)
        <div class="mt-3 p-3 bg-red-50 rounded-lg">
            <p class="text-sm font-semibold text-red-600">⚠️ Aggression history noted</p>
            @if($assessmentRequest->aggression_details)
            <p class="text-sm text-red-500 mt-1">{{ $assessmentRequest->aggression_details }}</p>
            @endif
        </div>
        @endif
    </div>

    <form action="{{ route('instructor.assessment.score.store', $assessmentRequest) }}" method="POST" class="space-y-4">
        @csrf

        @php
        $steps = [
            1 => ['title' => 'Step 1: Approach & Initial Reaction', 'desc' => 'How does the dog respond to the evaluator approaching?'],
            2 => ['title' => 'Step 2: Basic Control', 'desc' => 'Sit, down, stay — ability to perform basic cues'],
            3 => ['title' => 'Step 3: Walking on Lead', 'desc' => 'Loose-lead walking, responsiveness to handler'],
            4 => ['title' => 'Step 4: Reaction to Distractions', 'desc' => 'Response to environmental distractions'],
            5 => ['title' => 'Step 5: Interaction with Other Dogs', 'desc' => 'Behaviour when another dog is present'],
            6 => ['title' => 'Step 6: Handling & Examination', 'desc' => 'Tolerance of physical handling, ears, paws, mouth'],
            7 => ['title' => 'Step 7: Aggression Assessment', 'desc' => 'Specific aggression probing (skip if severe distress observed in Steps 1–6)'],
        ];
        @endphp

        @foreach($steps as $num => $step)
        <div class="card" :class="{{ $num === 7 }} && step7Skipped ? 'opacity-50' : ''">
            <div class="flex items-start justify-between gap-4 mb-3">
                <div>
                    <h3 class="font-semibold text-navy">{{ $step['title'] }}</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $step['desc'] }}</p>
                </div>
                @if($num === 7)
                <label class="flex items-center gap-2 cursor-pointer flex-shrink-0" @click.stop>
                    <input type="checkbox" name="step7_skipped" value="1" x-model="step7Skipped" class="rounded border-gray-300 text-amber focus:ring-amber">
                    <span class="text-sm text-amber font-medium">Skip</span>
                </label>
                @endif
            </div>

            <template x-if="{{ $num !== 7 }} || !step7Skipped">
                <div>
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                            @click="scores[{{ $num }}] = {{ $i }}"
                            :class="scores[{{ $num }}] === {{ $i }} ? 'bg-brand text-white border-brand' : 'border-gray-300 text-gray-600 hover:border-brand hover:text-brand'"
                            class="flex-1 py-2 rounded-lg border-2 text-sm font-semibold transition">
                            {{ $i }}
                        </button>
                        @endfor
                    </div>
                    <div class="flex justify-between text-xs text-gray-400 mt-1">
                        <span>Excellent</span>
                        <span>Severe concern</span>
                    </div>
                    <input type="hidden" name="step{{ $num }}_score" :value="scores[{{ $num }}] || ''">
                </div>
            </template>

            @if($num === 7)
            <div x-show="step7Skipped">
                <div>
                    <label class="form-label">Skip reason</label>
                    <input type="text" name="step7_skip_reason" value="{{ old('step7_skip_reason', $existing?->step7_skip_reason) }}" class="form-input" placeholder="e.g. Severe distress observed in Step 6">
                </div>
            </div>
            @endif
        </div>
        @endforeach

        {{-- Live Score Summary --}}
        <div class="card bg-navy text-white sticky bottom-4 shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/70 text-sm">Average Score</p>
                    <p class="text-3xl font-bold" x-text="avg.toFixed(1)">0.0</p>
                </div>
                <div class="text-right">
                    <p class="text-white/70 text-sm">Recommendation</p>
                    <p class="text-lg font-semibold" :class="recommendationColor" x-text="recommendation">—</p>
                </div>
            </div>
        </div>

        {{-- Notes & Outcome --}}
        <div class="card">
            <h3 class="font-semibold text-navy mb-4">Notes & Outcome</h3>

            <div class="mb-4">
                <label class="form-label">Staff Notes</label>
                <textarea name="staff_notes" rows="3" class="form-textarea">{{ old('staff_notes', $existing?->staff_notes) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Global Notes (visible to admin)</label>
                <textarea name="global_notes" rows="3" class="form-textarea">{{ old('global_notes', $existing?->global_notes) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Override Recommendation (optional)</label>
                <select name="final_outcome" class="form-select">
                    <option value="">Use calculated recommendation</option>
                    <option value="group_class" {{ old('final_outcome', $existing?->final_outcome) === 'group_class' ? 'selected' : '' }}>Group Class</option>
                    <option value="private_lessons" {{ old('final_outcome', $existing?->final_outcome) === 'private_lessons' ? 'selected' : '' }}>Private Lessons</option>
                    <option value="behaviourist" {{ old('final_outcome', $existing?->final_outcome) === 'behaviourist' ? 'selected' : '' }}>Behaviourist Referral</option>
                </select>
            </div>

            <div>
                <label class="form-label">Override Reason (if applicable)</label>
                <textarea name="override_reason" rows="2" class="form-textarea">{{ old('override_reason', $existing?->override_reason) }}</textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">Submit Score Sheet</button>
            <a href="{{ route('admin.assessments.show', $assessmentRequest) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
