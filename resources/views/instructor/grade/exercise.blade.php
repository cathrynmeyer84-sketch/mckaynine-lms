<x-app-layout :title="$exercise->name . ' — Grading'">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.grade.index', $class) }}" class="text-gray-400 hover:text-navy"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="page-title">{{ $exercise->name }}</h1>
            <p class="page-subtitle">{{ $class->name }} · {{ $exercise->starting_marks ? $exercise->starting_marks . ' marks' : '' }}</p>
        </div>
    </div>
</div>

<div class="page-content space-y-3">

    {{-- Exercise info --}}
    @if($exercise->description)
    <div class="card bg-gray-50 border-0">
        <p class="text-sm text-gray-600 leading-relaxed">{{ $exercise->description }}</p>
        @if($exercise->type === 'time' && $exercise->target_time_seconds)
        <p class="text-xs text-green-700 mt-1">Target: {{ $exercise->target_time_seconds }}s — marks awarded proportionally{{ $exercise->allow_second_attempt ? ' · 2nd attempt allowed' : '' }}</p>
        @endif
    </div>
    @endif

    @if($exercise->type === 'marks' && $exercise->deductionEvents->isNotEmpty())
    <div class="card bg-blue-50 border-0 py-2">
        <p class="text-xs font-semibold text-blue-700 mb-1">Deductions</p>
        <div class="flex flex-wrap gap-x-4 gap-y-1">
            @foreach($exercise->deductionEvents as $event)
            <span class="text-xs text-blue-800">{{ $event->event_name }} <span class="font-semibold">–{{ $event->marks_deducted }}</span></span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- One card per student --}}
    @forelse($enrolments as $enrolment)
    @php
        $existing = $enrolment->examResult?->exercise_scores[$exercise->id] ?? null;
        $savedScore = $existing['score'] ?? null;
    @endphp

    <div class="card" x-data="{
        @if($exercise->type === 'marks')
        counts: {{ Js::from(array_fill(0, $exercise->deductionEvents->count(), 0)) }},
        amounts: {{ Js::from($exercise->deductionEvents->pluck('marks_deducted')->map(fn($v) => (float)$v)->values()->toArray()) }},
        get deduction() { return this.counts.reduce((s,c,i) => s + c * this.amounts[i], 0); },
        get score() { return Math.max(0, {{ (float)($exercise->starting_marks ?? 0) }} - this.deduction); },
        inc(i) { this.counts[i]++; },
        dec(i) { if(this.counts[i] > 0) this.counts[i]--; },
        @elseif($exercise->type === 'rating')
        selectedLabel: '{{ $existing['label'] ?? '' }}',
        selectedDeduction: {{ $existing['deduction'] ?? 0 }},
        autoFail: {{ isset($existing['auto_fail']) && $existing['auto_fail'] ? 'true' : 'false' }},
        get score() { return Math.max(0, {{ (float)($exercise->starting_marks ?? 0) }} - this.selectedDeduction); },
        selectRating(label, deduction, isAutoFail) { this.selectedLabel = label; this.selectedDeduction = deduction; this.autoFail = isAutoFail; },
        @elseif($exercise->type === 'time')
        time1: {{ $existing['time1'] ?? 0 }},
        time2: {{ $existing['time2'] ?? 0 }},
        get bestTime() { return {{ $exercise->allow_second_attempt ? 'Math.max(this.time1, this.time2)' : 'this.time1' }}; },
        get score() {
            const pct = {{ $exercise->target_time_seconds ?? 1 }} > 0 ? Math.min(this.bestTime / {{ $exercise->target_time_seconds ?? 1 }}, 1) : 0;
            return Math.round(pct * {{ (float)($exercise->starting_marks ?? 0) }} * 100) / 100;
        },
        @endif
        saved: {{ $savedScore !== null ? 'true' : 'false' }}
    }">
        <div class="flex items-center justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand/10 flex items-center justify-center shrink-0 font-bold text-brand text-sm">
                    {{ strtoupper(substr($enrolment->dog->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $enrolment->dog->name }}</p>
                    <p class="text-xs text-gray-500">{{ $enrolment->handler->full_name }}</p>
                </div>
            </div>
            <div class="text-right shrink-0">
                @if($savedScore !== null)
                <span class="text-xs text-green-600 font-medium">Saved: {{ $savedScore }}/{{ $exercise->starting_marks }}</span>
                @endif
                <p class="text-sm font-bold text-navy"
                    x-text="score.toFixed(1) + '/{{ $exercise->starting_marks ?? 0 }}'"></p>
            </div>
        </div>

        {{-- Marks-based --}}
        @if($exercise->type === 'marks')
        <div class="divide-y divide-gray-50">
            @foreach($exercise->deductionEvents as $event)
            <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-700">{{ $event->event_name }}
                    <span class="text-xs text-gray-400">–{{ $event->marks_deducted }}</span>
                </span>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="dec({{ $loop->index }})"
                        :class="counts[{{ $loop->index }}] === 0 ? 'opacity-30' : 'hover:bg-red-100 hover:text-red-600'"
                        class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 font-bold flex items-center justify-center transition-colors">−</button>
                    <span class="w-5 text-center font-bold text-navy text-sm" x-text="counts[{{ $loop->index }}]">0</span>
                    <button type="button" @click="inc({{ $loop->index }})"
                        class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 font-bold flex items-center justify-center hover:bg-brand/10 hover:text-brand transition-colors">+</button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Rating-based --}}
        @elseif($exercise->type === 'rating')
        <div class="flex flex-wrap gap-2">
            @foreach($exercise->ratingScales as $scale)
            <button type="button"
                @click="selectRating('{{ $scale->label }}', {{ (float)$scale->marks_deducted }}, {{ $scale->is_automatic_fail ? 'true' : 'false' }})"
                :class="selectedLabel === '{{ $scale->label }}'
                    ? '{{ $scale->is_automatic_fail ? 'bg-red-600 border-red-600 text-white' : 'bg-navy border-navy text-white' }}'
                    : 'bg-white border-gray-200 text-gray-600 hover:border-navy'"
                class="flex-1 min-w-20 border-2 rounded-xl py-2 px-2 text-center font-semibold text-sm transition-all">
                {{ $scale->label }}
                @if($scale->is_automatic_fail)<span class="block text-xs opacity-70">Auto-fail</span>@endif
            </button>
            @endforeach
        </div>

        {{-- Time-based --}}
        @elseif($exercise->type === 'time')
        <div class="{{ $exercise->allow_second_attempt ? 'grid grid-cols-2 gap-3' : '' }}">
            <div>
                <label class="form-label text-xs">{{ $exercise->allow_second_attempt ? '1st Attempt (s)' : 'Time (seconds)' }}</label>
                <input type="number" min="0" step="0.1" class="form-input text-sm"
                    value="{{ $existing['time1'] ?? '' }}"
                    @input="time1 = parseFloat($el.value)||0">
            </div>
            @if($exercise->allow_second_attempt)
            <div>
                <label class="form-label text-xs">2nd Attempt (s)</label>
                <input type="number" min="0" step="0.1" class="form-input text-sm"
                    value="{{ $existing['time2'] ?? '' }}"
                    @input="time2 = parseFloat($el.value)||0">
            </div>
            @endif
        </div>
        @endif

        {{-- Save button --}}
        <form method="POST" action="{{ route('instructor.grade.exercise.save', [$class, $exercise, $enrolment]) }}" class="mt-3">
            @csrf
            <input type="hidden" name="score" :value="score">
            @if($exercise->type === 'rating')
            <input type="hidden" name="label" :value="selectedLabel">
            <input type="hidden" name="deduction" :value="selectedDeduction">
            <input type="hidden" name="auto_fail" :value="autoFail ? '1' : '0'">
            @elseif($exercise->type === 'time')
            <input type="hidden" name="time1" :value="time1">
            <input type="hidden" name="time2" :value="time2">
            @endif
            <button type="submit"
                :class="saved ? 'btn btn-sm w-full bg-green-600 border-green-600 text-white' : 'btn btn-primary btn-sm w-full'">
                <span x-show="!saved">Save</span>
                <span x-show="saved" x-cloak>✓ Saved — tap to update</span>
            </button>
        </form>
    </div>
    @empty
    <div class="empty-state"><p class="text-gray-500">No enrolled students.</p></div>
    @endforelse

</div>
</x-app-layout>
