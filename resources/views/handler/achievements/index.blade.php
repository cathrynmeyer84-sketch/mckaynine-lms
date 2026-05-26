@extends('layouts.app')

@section('title', 'My Achievements')

@section('content')
<div class="page-header">
    <h1 class="page-title">Achievements</h1>
</div>

<div class="page-content">
@php $grouped = $results->groupBy(fn($e) => $e->dog_id); @endphp

@forelse($grouped as $dogId => $enrolments)
@php $dog = $enrolments->first()->dog; @endphp

<div class="mb-10">

    {{-- Dog header --}}
    <div class="flex items-center gap-3 mb-5">
        @if($dog->photo_path)
        <img src="{{ Storage::url($dog->photo_path) }}" alt="{{ $dog->name }}"
             class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0">
        @else
        <div class="w-10 h-10 rounded-xl bg-navy/10 flex items-center justify-center flex-shrink-0 font-bold text-navy text-sm">
            {{ strtoupper(substr($dog->name, 0, 1)) }}
        </div>
        @endif
        <h2 class="text-lg font-bold text-navy">{{ $dog->name }}</h2>
        <span class="text-xs text-gray-400 font-medium">{{ $enrolments->count() }} {{ Str::plural('achievement', $enrolments->count()) }}</span>
    </div>

    {{-- 3-column grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($enrolments as $enrolment)
        @php
            $result   = $enrolment->examResult;
            $isPassed = in_array($result->achievement_level, ['pass', 'merit_pass', 'completed']);
            $isMerit  = $result->achievement_level === 'merit_pass';
            $rosette  = $enrolment->dogClass->classType?->rosette_image_path ?? null;
            $levelLabel = match($result->achievement_level) {
                'merit_pass' => 'Merit Pass',
                'pass'       => 'Pass',
                'completed'  => 'Completed',
                'review'     => 'Under Review',
                'fail'       => 'Did Not Pass',
                default      => ucfirst(str_replace('_', ' ', $result->achievement_level ?? '')),
            };
            $levelColor = match($result->achievement_level) {
                'merit_pass' => 'bg-blue-100 text-blue-800',
                'pass'       => 'bg-green-100 text-green-800',
                'completed'  => 'bg-green-100 text-green-800',
                'review'     => 'bg-amber/20 text-amber-700',
                'fail'       => 'bg-red-100 text-red-700',
                default      => 'bg-gray-100 text-gray-600',
            };
        @endphp

        <div class="card flex flex-col items-center text-center p-6 gap-0">

            {{-- Rosette / badge image --}}
            <div class="relative mb-5">
                @if($rosette)
                <img src="{{ Storage::url($rosette) }}" alt="Rosette"
                     class="w-36 h-36 object-contain drop-shadow-md">
                @else
                {{-- Default star rosette --}}
                <div class="relative w-36 h-36 flex items-center justify-center">
                    <svg class="w-full h-full text-amber drop-shadow" fill="currentColor" viewBox="0 0 100 100">
                        <!-- Ribbon loops -->
                        <ellipse cx="38" cy="80" rx="8" ry="14" transform="rotate(-15,38,80)" fill="#d4a030" opacity="0.9"/>
                        <ellipse cx="62" cy="80" rx="8" ry="14" transform="rotate(15,62,80)" fill="#d4a030" opacity="0.9"/>
                        <!-- Outer ring -->
                        <circle cx="50" cy="46" r="32" fill="#f0c040"/>
                        <!-- Middle ring -->
                        <circle cx="50" cy="46" r="25" fill="#e8a820"/>
                        <!-- Inner circle -->
                        <circle cx="50" cy="46" r="18" fill="#f5d060"/>
                        <!-- Star -->
                        <polygon points="50,30 54,42 67,42 57,50 61,63 50,55 39,63 43,50 33,42 46,42"
                                 fill="#c88010" opacity="0.85"/>
                    </svg>
                    @if($isMerit)
                    <div class="absolute -bottom-1 -right-1 bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">Merit</div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Class name --}}
            <h3 class="font-bold text-navy text-base leading-tight mb-1">
                {{ $enrolment->dogClass->name }}
            </h3>

            {{-- Date --}}
            @if($enrolment->dogClass->end_date)
            <p class="text-xs text-gray-400 mb-3">
                {{ $enrolment->dogClass->end_date->format('F Y') }}
            </p>
            @else
            <p class="text-xs text-gray-400 mb-3">Completed</p>
            @endif

            {{-- Achievement badge --}}
            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $levelColor }} mb-3">
                {{ $levelLabel }}
            </span>

            {{-- Score (only show for scored results, not simple completions) --}}
            @if($result->exam_type !== 'completion' && $result->total_score !== null)
            <p class="text-2xl font-bold text-navy">{{ number_format($result->total_score, 0) }}%</p>
            @endif

            {{-- Instructor comments --}}
            @if($result->instructor_comments)
            <div class="mt-3 pt-3 border-t border-gray-100 w-full text-left">
                <p class="text-xs font-semibold text-gray-500 mb-1">Instructor Notes</p>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $result->instructor_comments }}</p>
            </div>
            @endif

        </div>
        @endforeach
    </div>
</div>
@empty

<div class="empty-state">
    <div class="empty-state-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
        </svg>
    </div>
    <p class="text-gray-500">No achievements yet. Complete a class to earn your first rosette.</p>
</div>

@endforelse
</div>
@endsection
