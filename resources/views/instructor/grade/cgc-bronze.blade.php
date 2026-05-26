<x-app-layout :title="'CGC Bronze Grading'">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.grade.index', $class) }}" class="text-gray-400 hover:text-navy"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div><h1 class="page-title">CGC Bronze Grading</h1><p class="page-subtitle">{{ $enrolment->handler->full_name }} — {{ $enrolment->dog->name }}</p></div>
    </div>
</div>
<div class="page-content">
<form method="POST" action="{{ route('instructor.grade.cgc-bronze.store', [$class, $enrolment]) }}"
    x-data="{
        ratings: {
            test1: '{{ $grade?->test1_rating ?? '' }}', test2: '{{ $grade?->test2_rating ?? '' }}',
            test3a: '{{ $grade?->test3a_rating ?? '' }}', test3b: '{{ $grade?->test3b_rating ?? '' }}',
            test4: '{{ $grade?->test4_rating ?? '' }}', test5: '{{ $grade?->test5_rating ?? '' }}',
            test6: '{{ $grade?->test6_rating ?? '' }}', test7: '{{ $grade?->test7_rating ?? '' }}',
            test8: '{{ $grade?->test8_rating ?? '' }}', test9: '{{ $grade?->test9_rating ?? '' }}',
            test10: '{{ $grade?->test10_rating ?? '' }}', test11: '{{ $grade?->test11_rating ?? '' }}',
            test12: '{{ $grade?->test12_rating ?? '' }}', test13: '{{ $grade?->test13_rating ?? '' }}',
        },
        deductions: {
            test1:{excellent:0,very_good:-2,conditional:-5,not_ready:-10},
            test2:{excellent:0,very_good:-2,conditional:-5,not_ready:-10},
            test3a:{excellent:0,very_good:-1,conditional:-2,not_ready:-2},
            test3b:{excellent:0,very_good:-1,conditional:-2,not_ready:-3},
            test4:{excellent:0,very_good:-1,conditional:-3,not_ready:-5},
            test5:{excellent:0,very_good:-2,conditional:-4,not_ready:-10},
            test6:{excellent:0,very_good:-1,conditional:-3,not_ready:-5},
            test7:{excellent:0,very_good:-1,conditional:-4,not_ready:-10},
            test8:{excellent:0,very_good:-1,conditional:-3,not_ready:-10},
            test9:{excellent:0,very_good:-1,conditional:-2,not_ready:-5},
            test10:{excellent:0,very_good:-2,conditional:-4,not_ready:-10},
            test11:{excellent:0,very_good:-2,conditional:-4,not_ready:-10},
            test12:{excellent:0,very_good:-2,conditional:-3,not_ready:-5},
            test13:{excellent:0,very_good:-1,conditional:-3,not_ready:-5},
        },
        get totalScore() {
            let total = 100;
            for (const [test, rating] of Object.entries(this.ratings)) {
                if (rating && this.deductions[test] && this.deductions[test][rating] !== undefined) {
                    total += this.deductions[test][rating];
                }
            }
            return Math.max(0, total);
        },
        get hasBlockingFault() {
            return Object.values(this.ratings).some(r => r === 'conditional' || r === 'not_ready');
        },
        get achievementLevel() {
            if (this.hasBlockingFault) return { label: 'Not Ready', class: 'bg-red-100 text-red-700' };
            const s = this.totalScore;
            if (s >= 90) return { label: 'Excellent Pass', class: 'bg-green-100 text-green-700' };
            if (s >= 80) return { label: 'Pass', class: 'bg-blue-50 text-brand' };
            return { label: 'Not Ready', class: 'bg-red-100 text-red-700' };
        }
    }">
    @csrf

    {{-- Header info --}}
    <div class="card mb-4">
        <div class="grid grid-cols-2 gap-3">
            <div><label class="form-label">Evaluator Name</label><input type="text" name="evaluator_name" class="form-input" value="{{ $grade?->examResult?->evaluator_name ?? auth()->user()->name }}"></div>
            <div><label class="form-label">Exam Date</label><input type="date" name="exam_date" class="form-input" value="{{ $grade?->examResult?->exam_date?->format('Y-m-d') ?? today()->format('Y-m-d') }}"></div>
        </div>
    </div>

    {{-- Blocking fault warning --}}
    <div x-show="hasBlockingFault" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-red-700 font-semibold text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            Blocking Fault — Overall pass is not possible
        </p>
        <p class="text-red-600 text-xs mt-1">A Conditional or Not Ready rating prevents an overall pass regardless of total score.</p>
    </div>

    {{-- Tests --}}
    @php
    $tests = [
        ['test1', 'Accepting a stranger', 10],
        ['test2', 'Present for examination (incl. collar/lead check)', 10],
        ['test3a', 'Care & Responsibility', 2],
        ['test3b', 'Groom by handler', 3],
        ['test4', 'Play with or without a toy', 5],
        ['test5', 'Walk on lead without distraction', 10],
        ['test6', 'Walk on lead through door/gate', 5],
        ['test7', 'Reaction to another dog and handler', 10],
        ['test8', 'Walk on lead through crowd', 10],
        ['test9', 'Reaction to distraction', 5],
        ['test10', 'Release from lead, recall, attach lead', 10],
        ['test11', 'Lie down and stay to command', 10],
        ['test12', 'Supervised isolation', 5],
        ['test13', 'Food manners', 5],
    ];
    @endphp

    <div class="card space-y-0 divide-y divide-gray-100 mb-4">
        @foreach($tests as $i => [$key, $name, $maxMarks])
        <div class="py-4 first:pt-0 last:pb-0">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-navy">{{ str_replace(['test3a', 'test3b'], ['3a', '3b'], str_replace(['test'], [''], $key)) . '. ' . $name }}</p>
                <span class="text-xs text-gray-400">max {{ $maxMarks }}</span>
            </div>
            <div class="grid grid-cols-4 gap-1.5">
                @foreach(['excellent' => ['0', 'Excellent', 'bg-green-100 border-green-300 text-green-800'], 'very_good' => ['-', 'Very Good', 'bg-blue-50 border-blue-300 text-brand'], 'conditional' => ['C', 'Conditional', 'bg-amber/10 border-amber/40 text-amber-900'], 'not_ready' => ['NR', 'Not Ready', 'bg-red-50 border-red-300 text-red-700']] as $val => [$icon, $label, $activeClass])
                <label class="cursor-pointer">
                    <input type="radio" name="{{ $key }}_rating" value="{{ $val }}" class="sr-only" x-model="ratings.{{ $key }}">
                    <div :class="ratings.{{ $key }} === '{{ $val }}' ? '{{ $activeClass }}' : 'border-gray-200 text-gray-500 hover:border-gray-300'"
                         class="border rounded-lg py-1.5 text-center text-xs font-medium transition-all">
                        <span class="hidden sm:block">{{ $label }}</span>
                        <span class="sm:hidden">{{ $icon }}</span>
                    </div>
                </label>
                @endforeach
            </div>
            <input type="hidden" name="{{ $key }}_rating" :value="ratings.{{ $key }}">
        </div>
        @endforeach
    </div>

    {{-- Score summary --}}
    <div class="sticky bottom-20 lg:bottom-4 card border-2 border-navy/20 bg-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Score</p>
                <p class="text-3xl font-bold text-navy" x-text="totalScore + '/100'"></p>
            </div>
            <div>
                <p class="text-sm text-gray-500 text-right">Result</p>
                <span :class="achievementLevel.class" class="badge text-sm px-3 py-1" x-text="achievementLevel.label"></span>
            </div>
        </div>
    </div>

    <div class="mt-4 card">
        <label class="form-label">Global Comments</label>
        <textarea name="global_comments" class="form-textarea" rows="3" placeholder="Overall comments about the performance...">{{ $grade?->global_comments }}</textarea>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn-amber w-full btn-lg">Submit Grades for Admin Review</button>
    </div>
</form>
</div>
</x-app-layout>
