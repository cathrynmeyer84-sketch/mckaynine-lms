@extends('layouts.app')

@section('title', 'My Classes')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Classes</h1>
    <a href="{{ route('enrol.start') }}" class="btn btn-primary">Enrol Now</a>
</div>

<div class="page-content">
    @forelse($enrolments as $enrolment)
    <a href="{{ route('handler.classes.show', $enrolment) }}" class="card card-hover block mb-4">
        <div class="flex items-start justify-between mb-2">
            <div>
                <p class="font-semibold text-navy text-lg">
                    {{ $enrolment->dogClass?->name ?? 'Awaiting class assignment' }}
                </p>
                <p class="text-sm text-gray-500">{{ $enrolment->dog?->name }} @if($enrolment->dog?->breed) &middot; {{ $enrolment->dog->breed }} @endif</p>
            </div>
            <span class="badge badge-{{ $enrolment->status }}">{{ $enrolment->status_label }}</span>
        </div>
        <div class="flex flex-wrap gap-x-6 gap-y-1 text-sm text-gray-500 mt-3">
            @if($enrolment->dogClass?->start_date)
            <span>Starts {{ $enrolment->dogClass->start_date->format('d M Y') }}</span>
            @endif
            @if($enrolment->dogClass?->classType?->name)
            <span>{{ $enrolment->dogClass->classType->name }}</span>
            @endif
            @if($enrolment->examResult && $enrolment->examResult->is_released)
            <span class="text-green-600 font-medium">Result available</span>
            @endif
        </div>
    </a>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p class="text-gray-500">You haven't enrolled in any classes yet.</p>
        <a href="{{ route('enrol.start') }}" class="btn btn-primary mt-4">Enrol Now</a>
    </div>
    @endforelse
</div>
@endsection
