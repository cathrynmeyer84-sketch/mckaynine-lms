@extends('layouts.app')

@section('title', 'Choose Your Dog')

@section('content')
<div class="page-content max-w-xl mx-auto py-10 px-4">

    <div class="mb-8">
        <a href="{{ url()->previous() }}" class="text-sm text-gray-400 hover:text-navy inline-flex items-center gap-1 mb-4">
            ← Back
        </a>
        <h1 class="text-2xl font-bold text-navy">Which dog would you like to enrol?</h1>
        @if($classType)
        <p class="text-gray-500 mt-1 text-sm">Enrolling in <strong>{{ $classType->name }}</strong></p>
        @endif
    </div>

    <div class="space-y-4">

        {{-- Existing dogs --}}
        @foreach($dogs as $dog)
        <div class="card {{ $dog->eligible ? '' : 'opacity-60' }}">
            <div class="flex items-center gap-4">
                @if($dog->photo_path)
                <img src="{{ Storage::url($dog->photo_path) }}" alt="{{ $dog->name }}"
                    class="w-14 h-14 rounded-full object-cover flex-shrink-0 border border-gray-200">
                @else
                <div class="w-14 h-14 rounded-full bg-brand-beige flex items-center justify-center flex-shrink-0 border border-gray-200">
                    <svg class="w-7 h-7 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z"/>
                    </svg>
                </div>
                @endif

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-navy">{{ $dog->name }}</p>
                    @if($dog->breed)
                    <p class="text-xs text-gray-400">{{ $dog->breed }}</p>
                    @endif

                    @if(!$dog->eligible)
                    <div class="mt-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1.5 leading-snug">
                        Needs to complete one of:
                        @foreach($dog->missingPrereqs as $prereq)
                        <span class="font-semibold">{{ $prereq->name }}</span>@unless($loop->last) or @endunless
                        @endforeach
                    </div>
                    @endif
                </div>

                @if($dog->eligible)
                <form method="POST" action="{{ route('enrol.existing-dog.store') }}">
                    @csrf
                    <input type="hidden" name="dog_id" value="{{ $dog->id }}">
                    <input type="hidden" name="class_id" value="{{ $classId }}">
                    <input type="hidden" name="pathway" value="{{ request('pathway', 'existing') }}">
                    <button type="submit" class="btn btn-primary flex-shrink-0 text-sm whitespace-nowrap">
                        Enrol {{ $dog->name }} →
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Add a new dog --}}
        <div class="card border-dashed border-2 border-gray-200 bg-transparent">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-700">Add a new dog</p>
                    <p class="text-xs text-gray-400">We'll check their age to find the right starting point</p>
                </div>
                <a href="{{ route('enrol.start') }}{{ $classId ? '?class_id='.$classId : '' }}"
                    class="btn btn-secondary flex-shrink-0 text-sm whitespace-nowrap">
                    Add dog →
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
