<x-app-layout :title="'Grading — ' . $class->name">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.classes.show', $class) }}" class="text-gray-400 hover:text-navy"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div><h1 class="page-title">Grading</h1><p class="page-subtitle">{{ $class->name }}</p></div>
    </div>
</div>

<div class="page-content" x-data="{ tab: 'students' }">

    <div class="flex border-b border-gray-200 mb-4">
        <button @click="tab='students'" :class="tab==='students' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
            class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">By Student</button>
        <button @click="tab='exercises'" :class="tab==='exercises' ? 'border-brand text-brand' : 'border-transparent text-gray-500'"
            class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">By Exercise</button>
    </div>

    {{-- ── BY STUDENT ── --}}
    <div x-show="tab==='students'" class="space-y-2">
        @forelse($enrolments as $enrolment)
        @php $result = $enrolment->examResult; @endphp
        <div class="card">
            <a href="{{ route('instructor.grade.form', [$class, $enrolment]) }}"
                class="flex items-center justify-between gap-3 hover:opacity-80 transition-opacity">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand/10 flex items-center justify-center shrink-0 font-bold text-brand text-sm">
                        {{ strtoupper(substr($enrolment->dog->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $enrolment->dog->name }}</p>
                        <p class="text-sm text-gray-500">{{ $enrolment->handler->full_name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($result)
                        <span class="text-sm font-semibold text-navy">{{ number_format($result->total_score, 1) }}%</span>
                        @php
                            $levelClass = match($result->achievement_level) {
                                'merit_pass' => 'badge-active',
                                'pass'       => 'badge-active',
                                'review'     => 'badge-pending',
                                default      => 'bg-red-100 text-red-700 badge',
                            };
                        @endphp
                        <span class="badge {{ $levelClass }} text-xs">{{ ucwords(str_replace('_', ' ', $result->achievement_level)) }}</span>
                    @else
                        <span class="text-xs text-gray-400">Not graded</span>
                    @endif
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @if($result && $result->status === 'draft')
            <div class="mt-3 pt-3 border-t border-gray-100">
                <form method="POST" action="{{ route('instructor.grade.submit', [$class, $enrolment]) }}">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm w-full">
                        Submit for Admin Review
                    </button>
                </form>
            </div>
            @elseif($result && $result->status === 'submitted')
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-center text-amber font-medium">Submitted — awaiting admin release</p>
            </div>
            @elseif($result && $result->status === 'released')
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-center text-green-600 font-medium">Released to handler</p>
            </div>
            @endif
        </div>
        @empty
        <div class="empty-state"><p class="text-gray-500">No enrolled students.</p></div>
        @endforelse
    </div>

    {{-- ── BY EXERCISE ── --}}
    <div x-show="tab==='exercises'" class="space-y-2">
        @forelse($exercises as $exercise)
        @php
            $gradedCount = $enrolments->filter(fn($e) => isset($e->examResult?->exercise_scores[$exercise->id]))->count();
            $badgeClass = match($exercise->type) {
                'marks'  => 'bg-blue-100 text-blue-700',
                'rating' => 'bg-purple-100 text-purple-700',
                'time'   => 'bg-green-100 text-green-700',
                default  => 'bg-gray-100 text-gray-700',
            };
        @endphp
        <a href="{{ route('instructor.grade.exercise', [$class, $exercise]) }}"
            class="card flex items-center justify-between gap-3 hover:bg-gray-50 transition-colors cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $badgeClass }} flex items-center justify-center shrink-0 font-bold text-sm">
                    {{ $loop->iteration }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ $exercise->name }}</p>
                    <p class="text-sm text-gray-500">
                        {{ match($exercise->type) { 'marks' => 'Marks-based', 'rating' => 'Rating-based', 'time' => 'Time-based', default => $exercise->type } }}
                        @if($exercise->starting_marks) · {{ $exercise->starting_marks }} marks @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs text-gray-400">{{ $gradedCount }}/{{ $enrolments->count() }} graded</span>
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <p class="text-gray-500">No grading exercises set up.</p>
            <p class="text-xs text-gray-400 mt-1">Add them in Admin → Class Types → {{ $class->classType?->name }}.</p>
        </div>
        @endforelse
    </div>

</div>
</x-app-layout>
