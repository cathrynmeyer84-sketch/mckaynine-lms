<x-app-layout :title="'Grading'">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.grade.index', $class) }}" class="text-gray-400 hover:text-navy"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="page-title">{{ $class->classType?->name ?? 'Grading' }}</h1>
            <p class="page-subtitle">{{ $enrolment->handler->full_name }} — {{ $enrolment->dog->name }}</p>
        </div>
    </div>
</div>

<div class="page-content">

@if($exercises->isEmpty())
<div class="empty-state">
    <p class="text-gray-500">No grading exercises have been set up for this class type.</p>
    <p class="text-xs text-gray-400 mt-1">Add them in Admin → Class Types → {{ $class->classType?->name }}.</p>
</div>
@else

@php
// Build Alpine data structure from DB exercises
$alpineExs = [];
foreach ($exercises as $ex) {
    $entry = [
        'type'      => $ex->type,
        'maxMarks'  => (float)($ex->starting_marks ?? 0),
        'deduction' => 0,
        'autoFail'  => false,
    ];
    if ($ex->type === 'marks') {
        $entry['counts']  = array_fill(0, $ex->deductionEvents->count(), 0);
        $entry['amounts'] = $ex->deductionEvents->pluck('marks_deducted')->map(fn($v) => (float)$v)->values()->toArray();
    } elseif ($ex->type === 'rating') {
        $entry['selectedLabel'] = null;
    } elseif ($ex->type === 'time') {
        $entry['targetSeconds'] = (int)$ex->target_time_seconds;
        $entry['allowSecond']   = (bool)$ex->allow_second_attempt;
        $entry['time1'] = 0;
        $entry['time2'] = 0;
    }
    $alpineExs[(string)$ex->id] = $entry;
}
$maxTotal = $exercises->sum(fn($e) => (float)($e->starting_marks ?? 0));
@endphp

<form method="POST" action="{{ route('instructor.grade.eo.store', [$class, $enrolment]) }}"
    x-data="{
        exs: {{ Js::from($alpineExs) }},
        inc(id, i)  { this.exs[id].counts[i]++; this.recalcMarks(id); },
        dec(id, i)  { if (this.exs[id].counts[i] > 0) this.exs[id].counts[i]--; this.recalcMarks(id); },
        recalcMarks(id) {
            this.exs[id].deduction = this.exs[id].counts.reduce((s, c, i) => s + c * this.exs[id].amounts[i], 0);
        },
        selectRating(id, deduction, isAutoFail, label) {
            this.exs[id].deduction   = deduction;
            this.exs[id].autoFail    = isAutoFail;
            this.exs[id].selectedLabel = label;
        },
        calcTime(id) {
            const best = this.exs[id].allowSecond
                ? Math.max(this.exs[id].time1 || 0, this.exs[id].time2 || 0)
                : (this.exs[id].time1 || 0);
            const pct = this.exs[id].targetSeconds > 0 ? Math.min(best / this.exs[id].targetSeconds, 1) : 0;
            this.exs[id].deduction = Math.round((1 - pct) * this.exs[id].maxMarks * 100) / 100;
        },
        exScore(id) { return Math.max(0, this.exs[id].maxMarks - this.exs[id].deduction); },
        get totalScore() { return Object.keys(this.exs).reduce((sum, id) => sum + this.exScore(id), 0); },
        get hasAutoFail() { return Object.values(this.exs).some(e => e.autoFail); },
        get resultLevel() {
            if (this.hasAutoFail) return { label: 'Fail (Auto)', cls: 'bg-red-100 text-red-700' };
            const pct = {{ $maxTotal }} > 0 ? (this.totalScore / {{ $maxTotal }}) * 100 : 0;
            if (pct >= 91) return { label: 'Merit Pass', cls: 'bg-green-100 text-green-800' };
            if (pct >= 80) return { label: 'Pass',       cls: 'bg-blue-50 text-brand' };
            if (pct >= 70) return { label: 'Review',     cls: 'bg-amber-50 text-amber-800' };
            return             { label: 'Fail',          cls: 'bg-red-50 text-red-700' };
        }
    }">
    @csrf
    <input type="hidden" name="max_total" value="{{ $maxTotal }}">
    <input type="hidden" name="has_auto_fail" :value="hasAutoFail ? '1' : '0'">

    {{-- Header --}}
    <div class="card mb-4">
        <div class="grid grid-cols-2 gap-3">
            <div><label class="form-label">Evaluator Name</label><input type="text" name="evaluator_name" class="form-input" value="{{ auth()->user()->name }}"></div>
            <div><label class="form-label">Exam Date</label><input type="date" name="exam_date" class="form-input" value="{{ today()->format('Y-m-d') }}"></div>
        </div>
    </div>

    {{-- Exercises --}}
    <div class="space-y-3 mb-4">
        @foreach($exercises as $ex)
        <div class="card">
            {{-- Exercise header --}}
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="font-semibold text-navy">{{ $loop->iteration }}. {{ $ex->name }}</p>
                    @if($ex->description)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $ex->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($ex->starting_marks)
                    <span class="text-xs text-gray-400">max {{ $ex->starting_marks }}</span>
                    @endif
                    <span class="text-sm font-bold text-brand" x-text="exScore('{{ $ex->id }}') + '/{{ $ex->starting_marks ?? 0 }}'"></span>
                </div>
            </div>

            {{-- ── MARKS-BASED ── --}}
            @if($ex->type === 'marks')
            @if($ex->deductionEvents->isEmpty())
            <p class="text-xs text-gray-400 text-center py-2">No deduction events set up.</p>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($ex->deductionEvents as $event)
                <div class="flex items-center justify-between py-2.5">
                    <div>
                        <span class="text-sm text-gray-800">{{ $event->event_name }}</span>
                        <span class="text-xs text-gray-400 ml-1">–{{ $event->marks_deducted }} each</span>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button type="button"
                            @click="dec('{{ $ex->id }}', {{ $loop->index }})"
                            :class="exs['{{ $ex->id }}'].counts[{{ $loop->index }}] === 0 ? 'opacity-30 cursor-default' : 'hover:bg-red-100 hover:text-red-600'"
                            class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold text-lg flex items-center justify-center transition-colors">−</button>
                        <span class="w-6 text-center font-bold text-navy tabular-nums"
                            x-text="exs['{{ $ex->id }}'].counts[{{ $loop->index }}]">0</span>
                        <button type="button"
                            @click="inc('{{ $ex->id }}', {{ $loop->index }})"
                            class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 font-bold text-lg flex items-center justify-center hover:bg-brand/10 hover:text-brand transition-colors">+</button>
                    </div>
                </div>
                @endforeach
                <div class="pt-2 flex justify-end">
                    <span class="text-xs text-gray-400">Total deduction:</span>
                    <span class="text-xs font-semibold text-red-500 ml-1" x-text="'–' + exs['{{ $ex->id }}'].deduction"></span>
                </div>
            </div>
            @endif

            {{-- ── RATING-BASED ── --}}
            @elseif($ex->type === 'rating')
            @if($ex->ratingScales->isEmpty())
            <p class="text-xs text-gray-400 text-center py-2">No rating scale set up.</p>
            @else
            <div class="flex flex-wrap gap-2">
                @foreach($ex->ratingScales as $scale)
                <button type="button"
                    @click="selectRating('{{ $ex->id }}', {{ (float)$scale->marks_deducted }}, {{ $scale->is_automatic_fail ? 'true' : 'false' }}, '{{ $scale->label }}')"
                    :class="exs['{{ $ex->id }}'].selectedLabel === '{{ $scale->label }}'
                        ? '{{ $scale->is_automatic_fail ? 'bg-red-600 border-red-600 text-white' : 'bg-navy border-navy text-white' }}'
                        : 'bg-white border-gray-200 text-gray-600 hover:border-navy'"
                    class="flex-1 min-w-24 border-2 rounded-xl py-2.5 px-3 text-center font-semibold text-sm transition-all">
                    {{ $scale->label }}
                    @if($scale->marks_deducted > 0)
                    <span class="block text-xs font-normal mt-0.5 opacity-70">–{{ $scale->marks_deducted }} pts</span>
                    @else
                    <span class="block text-xs font-normal mt-0.5 opacity-70">no deduction</span>
                    @endif
                    @if($scale->is_automatic_fail)
                    <span class="block text-xs font-normal opacity-70">⚠ Auto-fail</span>
                    @endif
                </button>
                @endforeach
            </div>
            @endif

            {{-- ── TIME-BASED ── --}}
            @elseif($ex->type === 'time')
            <div class="space-y-3">
                @if($ex->target_time_seconds)
                <div class="text-xs text-green-700 bg-green-50 rounded-lg p-2">
                    Target: {{ $ex->target_time_seconds }}s — marks awarded proportionally to time achieved
                    @if($ex->allow_second_attempt) · 2nd attempt allowed (higher counts) @endif
                </div>
                @endif
                <div class="{{ $ex->allow_second_attempt ? 'grid grid-cols-2 gap-3' : '' }}">
                    <div>
                        <label class="form-label text-xs">{{ $ex->allow_second_attempt ? '1st Attempt (seconds)' : 'Time achieved (seconds)' }}</label>
                        <input type="number" min="0" step="0.1" class="form-input text-sm" placeholder="0"
                            @input="exs['{{ $ex->id }}'].time1 = parseFloat($el.value)||0; calcTime('{{ $ex->id }}')">
                    </div>
                    @if($ex->allow_second_attempt)
                    <div>
                        <label class="form-label text-xs">2nd Attempt (seconds)</label>
                        <input type="number" min="0" step="0.1" class="form-input text-sm" placeholder="0"
                            @input="exs['{{ $ex->id }}'].time2 = parseFloat($el.value)||0; calcTime('{{ $ex->id }}')">
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Hidden score for submission --}}
            <input type="hidden" name="ex_scores[{{ $ex->id }}]" :value="exScore('{{ $ex->id }}')">
        </div>
        @endforeach
    </div>

    {{-- Score summary --}}
    <div class="sticky bottom-20 lg:bottom-4 card border-2 border-navy/20 bg-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Score</p>
                <p class="text-3xl font-bold text-navy" x-text="totalScore.toFixed(1) + '/{{ $maxTotal }}'"></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Result</p>
                <span :class="resultLevel.cls" class="badge text-sm px-3 py-1" x-text="resultLevel.label"></span>
            </div>
        </div>
        <div x-show="hasAutoFail" x-cloak class="mt-2 text-xs text-red-600 font-medium">
            ⚠ An automatic fail rating has been selected.
        </div>
    </div>

    <div class="mt-4 card">
        <label class="form-label">Comments</label>
        <textarea name="global_comments" class="form-textarea" rows="3" placeholder="Overall comments..."></textarea>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn-amber w-full btn-lg">Submit Grades for Admin Review</button>
    </div>
</form>

@endif
</div>
</x-app-layout>
