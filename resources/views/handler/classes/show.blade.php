@extends('layouts.app')

@section('title', $enrolment->dogClass->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $enrolment->dogClass->name }}</h1>
        <p class="page-subtitle">{{ $enrolment->dog->name }} &middot; {{ $enrolment->dog->breed }}</p>
    </div>
    <span class="badge badge-{{ $enrolment->status }}">{{ ucfirst($enrolment->status) }}</span>
</div>

<div class="page-content" x-data="{ tab: new URLSearchParams(window.location.search).get('tab') || 'schedule' }">

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6 overflow-x-auto">
        <button @click="tab='schedule'" :class="tab==='schedule' ? 'bg-white shadow text-navy' : 'text-gray-500'" class="flex-1 min-w-max px-4 py-2 rounded-lg text-sm font-medium transition">Schedule</button>
        @if($enrolment->examResult && $enrolment->examResult->is_released)
        <button @click="tab='result'" :class="tab==='result' ? 'bg-white shadow text-navy' : 'text-gray-500'" class="flex-1 min-w-max px-4 py-2 rounded-lg text-sm font-medium transition">My Result</button>
        @endif
        @if($enrolment->goals->isNotEmpty())
        <button @click="tab='goals'" :class="tab==='goals' ? 'bg-white shadow text-navy' : 'text-gray-500'" class="flex-1 min-w-max px-4 py-2 rounded-lg text-sm font-medium transition">Goals</button>
        @endif
        @if($enrolment->dogClass->status === 'completed' && !$enrolment->survey)
        <button @click="tab='survey'" :class="tab==='survey' ? 'bg-white shadow text-navy' : 'text-gray-500'" class="flex-1 min-w-max px-4 py-2 rounded-lg text-sm font-medium transition">Feedback</button>
        @endif
    </div>

    {{-- Schedule Tab --}}
    <div x-show="tab==='schedule'">
        <div class="space-y-3">
            @foreach($enrolment->dogClass->classDates->where('is_off_week', false) as $classDate)
            @php
                $register = $enrolment->registers->where('class_date_id', $classDate->id)->first();
                $hasContent = $classDate->isContentPublished();
            @endphp
            <div class="card @if($hasContent) card-hover @endif">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $classDate->date->isPast() ? 'bg-gray-100' : 'bg-brand/10' }} flex items-center justify-center flex-shrink-0 text-center">
                            <div class="text-xs font-bold {{ $classDate->date->isPast() ? 'text-gray-400' : 'text-brand' }}">
                                <div>{{ $classDate->date->format('d') }}</div>
                                <div>{{ $classDate->date->format('M') }}</div>
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-navy">Week {{ $classDate->week_number ?? loop->iteration }}</p>
                            <p class="text-sm text-gray-500">{{ $classDate->date->format('l, d M') }} &middot; {{ $classDate->start_time }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($register)
                            @if($register->attended)
                            <span class="text-green-600 text-xs font-medium">Attended</span>
                            @else
                            <span class="text-red-500 text-xs font-medium">Absent</span>
                            @endif
                        @endif
                        @if($hasContent)
                        <a href="{{ route('handler.classes.week', [$enrolment, $classDate]) }}" class="btn btn-outline btn-sm">View</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Result Tab --}}
    @if($enrolment->examResult && $enrolment->examResult->is_released)
    <div x-show="tab==='result'">
        <div class="card">
            <h3 class="text-lg font-semibold text-navy mb-4">Your Result</h3>
            <div class="flex items-center gap-4 mb-6">
                <div class="text-5xl font-bold text-brand">{{ number_format($enrolment->examResult->total_score, 1) }}<span class="text-2xl text-gray-400">%</span></div>
                <div>
                    <p class="text-gray-500 text-sm">Score</p>
                    @if($enrolment->examResult->achievement_level)
                    <p class="font-semibold text-navy capitalize mt-1">{{ str_replace('_', ' ', $enrolment->examResult->achievement_level) }}</p>
                    @endif
                </div>
            </div>
            {{-- Exercise breakdown --}}
            @php
                $exercises = $enrolment->dogClass->classType->gradingExercises ?? collect();
                $rawScores = $enrolment->examResult->exercise_scores ?? [];
                // Normalise keys to strings to match JSON decoded array keys
                $scores = array_combine(array_map('strval', array_keys($rawScores)), array_values($rawScores));
            @endphp
            @if($exercises->isNotEmpty() && !empty($scores))
            <div class="mt-6">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Exercise Breakdown</p>
                <div class="space-y-2">
                    @foreach($exercises as $exercise)
                    @php
                        $exScore = $scores[(string) $exercise->id]['score'] ?? null;
                        $max = $exercise->max_marks;
                        $pct = ($max && $exScore !== null) ? min(100, ($exScore / $max) * 100) : null;
                    @endphp
                    @if($exScore !== null)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <p class="text-sm text-gray-700">{{ $exercise->name }}</p>
                        <p class="text-sm font-semibold text-navy shrink-0">
                            {{ number_format($exScore, 1) }}@if($max) / {{ number_format($max, 0) }}@endif
                        </p>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($enrolment->examResult->instructor_comments)
            <div class="bg-gray-50 rounded-xl p-4 mt-4">
                <p class="text-sm font-medium text-gray-700 mb-1">Instructor Notes</p>
                <p class="text-gray-600">{{ $enrolment->examResult->instructor_comments }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Goals Tab --}}
    @if($enrolment->goals->isNotEmpty())
    <div x-show="tab==='goals'">
        <div class="space-y-3">
            @foreach($enrolment->goals as $goal)
            <div class="card">
                <p class="text-navy">{{ $goal->goal }}</p>
                @if($goal->progress_notes)
                <p class="text-sm text-gray-500 mt-2">{{ $goal->progress_notes }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Survey Tab --}}
    @if($enrolment->dogClass->status === 'completed' && !$enrolment->survey)
    <div x-show="tab==='survey'">
        <div class="card text-center py-8">
            <p class="text-gray-600 mb-4">We'd love your feedback on the class!</p>
            <a href="{{ route('handler.survey.form', $enrolment) }}" class="btn btn-primary">Leave Feedback</a>
        </div>
    </div>
    @endif

</div>
@endsection
