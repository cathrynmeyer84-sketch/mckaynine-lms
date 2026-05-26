@extends('layouts.app')

@section('title', 'Book Your Assessment')

@section('content')
<div class="page-content max-w-xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-navy mb-1">Book Your Assessment</h1>
        <p class="text-gray-500">Hi {{ $assessmentRequest->handler?->first_name }}, choose a date and time that works for you.</p>
    </div>

    @if($slots->isEmpty())
    <div class="card text-center py-10">
        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-gray-700 font-medium mb-1">No slots available right now</p>
        <p class="text-sm text-gray-400">We'll be in touch soon to arrange a suitable time.</p>
    </div>
    @else
    <form action="{{ route('enrol.public-slot.book', $assessmentRequest) }}" method="POST" class="space-y-2">
        @csrf

        @php $currentDate = null; @endphp
        @foreach($slots as $availableSlot)
            @if($availableSlot->date->format('Y-m-d') !== $currentDate)
                @if($currentDate !== null)</div>@endif
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mt-6 mb-2 px-1">
                    {{ $availableSlot->date->format('l, d F Y') }}
                </h3>
                <div class="space-y-2">
                @php $currentDate = $availableSlot->date->format('Y-m-d'); @endphp
            @endif
            <label class="card cursor-pointer block border-2 border-transparent has-[:checked]:border-brand has-[:checked]:bg-brand/5 transition-colors">
                <div class="flex items-center gap-3">
                    <input type="radio" name="slot_key" value="{{ $availableSlot->key }}" required class="text-brand focus:ring-brand shrink-0">
                    <div class="flex-1">
                        <p class="font-semibold text-navy">{{ \Carbon\Carbon::parse($availableSlot->start_time)->format('g:i A') }}</p>
                        @if($availableSlot->notes)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $availableSlot->notes }}</p>
                        @endif
                    </div>
                    <span class="text-xs text-gray-400 shrink-0">{{ $availableSlot->remaining }} spot(s) left</span>
                </div>
            </label>
        @endforeach
        @if($currentDate !== null)</div>@endif

        @error('slot_key')<p class="form-error mt-2">{{ $message }}</p>@enderror

        <button type="submit" class="btn btn-primary w-full mt-6">Confirm Booking →</button>
    </form>
    @endif
</div>
@endsection
