@extends('layouts.app')

@section('title', 'Book Assessment Slot')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Book Your Assessment</h1>
        <p class="page-subtitle">Choose a time that works for you</p>
    </div>
</div>

<div class="page-content max-w-xl">
    @if($slots->isEmpty())
    <div class="card text-center py-10">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-gray-500 mb-2">No assessment slots are currently available.</p>
        <p class="text-sm text-gray-400">We'll be in touch to schedule your assessment once slots open up.</p>
        <a href="{{ route('enrol.submitted') }}" class="btn btn-primary mt-6">Continue</a>
    </div>
    @else
    <form action="{{ route('enrol.slot.book', $assessmentRequest) }}" method="POST" class="space-y-4">
        @csrf

        @php $currentDate = null; @endphp
        @foreach($slots as $slot)
            @if($slot->date->format('Y-m-d') !== $currentDate)
                @if($currentDate !== null)</div>@endif
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mt-6 mb-2">
                    {{ $slot->date->format('l, d F Y') }}
                </h3>
                <div class="space-y-2">
                @php $currentDate = $slot->date->format('Y-m-d'); @endphp
            @endif
            <label class="card cursor-pointer block has-[:checked]:border-brand has-[:checked]:bg-brand/5 border-2 border-transparent transition">
                <div class="flex items-center gap-3">
                    <input type="radio" name="slot_key" value="{{ $slot->key }}" required class="text-brand focus:ring-brand">
                    <div>
                        <p class="font-medium text-navy">{{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}</p>
                        @if($slot->notes)
                        <p class="text-sm text-gray-500">{{ $slot->notes }}</p>
                        @endif
                        <p class="text-xs text-gray-400">{{ $slot->remaining }} spot(s) remaining</p>
                    </div>
                </div>
            </label>
        @endforeach
        @if($currentDate !== null)</div>@endif

        @error('slot_key')<p class="form-error">{{ $message }}</p>@enderror

        <button type="submit" class="btn btn-primary w-full mt-6">Confirm Booking</button>
    </form>
    @endif
</div>
@endsection
