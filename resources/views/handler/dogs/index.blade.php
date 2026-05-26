@extends('layouts.app')

@section('title', 'My Dogs')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Dogs</h1>
</div>

<div class="page-content">
    @forelse($dogs as $dog)
    <div class="card mb-4">
        <div class="flex items-start gap-4">
            @if($dog->photo_path)
            <img src="{{ Storage::url($dog->photo_path) }}" alt="{{ $dog->name }}" class="w-20 h-20 rounded-2xl object-cover flex-shrink-0">
            @else
            <div class="w-20 h-20 rounded-2xl bg-stone/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-10 h-10 text-stone" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11.5A6.5 6.5 0 0 1 11 5h2a6.5 6.5 0 0 1 6.5 6.5A4.5 4.5 0 0 1 15 16v1a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1v-1a4.5 4.5 0 0 1-4.5-4.5Z"/></svg>
            </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="font-semibold text-navy text-lg">{{ $dog->name }}</h2>
                    <a href="{{ route('handler.dogs.edit', $dog) }}" class="btn btn-outline btn-sm flex-shrink-0">Edit</a>
                </div>
                <p class="text-gray-500 text-sm">{{ $dog->breed ?? 'Mixed breed' }}</p>
                @if($dog->age_in_months !== null)
                <p class="text-sm text-gray-400 mt-1">{{ $dog->age_in_months }} months old</p>
                @endif
                <div class="flex flex-wrap gap-3 mt-3">
                    @if($dog->microchip_number)
                    <span class="text-xs text-gray-500">Microchip: {{ $dog->microchip_number }}</span>
                    @endif
                    @if($dog->vaccination_expiry)
                    <span class="text-xs {{ $dog->vaccinationExpiringSoon() ? 'text-amber font-semibold' : 'text-gray-500' }}">
                        Vaccines expire: {{ $dog->vaccination_expiry->format('d M Y') }}
                        @if($dog->vaccinationExpiringSoon()) ⚠️ @endif
                    </span>
                    @endif
                </div>
                @if($dog->enrolments->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($dog->enrolments->where('status', 'confirmed') as $enrolment)
                    <span class="text-xs bg-brand/10 text-brand rounded-full px-3 py-1">{{ $enrolment->dogClass->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-gray-500">No dogs on your profile yet. Enrol to add your first dog.</p>
        <a href="{{ route('enrol.start') }}" class="btn btn-primary mt-4">Get Started</a>
    </div>
    @endforelse
</div>
@endsection
