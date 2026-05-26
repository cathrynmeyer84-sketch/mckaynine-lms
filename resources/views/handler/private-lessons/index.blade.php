<x-app-layout title="Private Lessons">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Private Lessons</h1>
            <p class="page-subtitle">Your one-to-one training sessions</p>
        </div>
        <a href="{{ route('handler.private-lessons.book') }}" class="btn-primary">Book a Lesson</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if($upcoming->count())
    <div class="mb-8">
        <h2 class="text-base font-semibold text-navy mb-3">Upcoming &amp; Pending</h2>
        <div class="space-y-3">
            @foreach($upcoming as $lesson)
            <div class="card">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold text-navy">{{ $lesson->instructor?->full_name }}</p>
                            <span class="badge {{ $lesson->status_badge_class }}">{{ $lesson->status_label }}</span>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ $lesson->dog?->name }}
                            <span class="text-gray-400">·</span>
                            {{ $lesson->requested_date?->format('l, d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}
                        </p>

                        @if($lesson->status === 'reschedule_requested' && $lesson->reschedule_note)
                        <div class="mt-3 p-3 bg-amber/10 border border-amber/30 rounded-xl">
                            <p class="text-xs font-semibold text-amber mb-1">Reschedule Requested</p>
                            <p class="text-sm text-gray-700">{{ $lesson->reschedule_note }}</p>
                            <a href="{{ route('handler.private-lessons.book') }}" class="inline-block mt-2 text-xs text-brand underline">Book a new slot</a>
                        </div>
                        @endif
                    </div>

                    @if(in_array($lesson->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('handler.private-lessons.cancel', $lesson) }}" class="shrink-0">
                        @csrf
                        <button type="submit" class="btn-outline btn-sm text-red-400 border-red-200 hover:bg-red-50"
                            onclick="return confirm('Cancel this lesson request?')">Cancel</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($past->count())
    <div>
        <h2 class="text-base font-semibold text-navy mb-3">Past Lessons</h2>
        <div class="space-y-3">
            @foreach($past as $lesson)
            <div class="card">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-medium text-gray-800">{{ $lesson->instructor?->full_name }}</p>
                            <span class="badge {{ $lesson->status_badge_class }}">{{ $lesson->status_label }}</span>
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ $lesson->dog?->name }}
                            <span class="text-gray-300">·</span>
                            {{ $lesson->requested_date?->format('d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}
                        </p>
                        @if($lesson->status === 'completed' && $lesson->instructor_notes)
                        <div class="mt-3 p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs font-semibold text-gray-500 mb-1">Session Notes</p>
                            <p class="text-sm text-gray-700">{{ $lesson->instructor_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($upcoming->isEmpty() && $past->isEmpty())
    <div class="card text-center py-16">
        <div class="w-12 h-12 bg-navy/5 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-navy/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <p class="text-gray-500 mb-4">You haven't booked any private lessons yet.</p>
        <a href="{{ route('handler.private-lessons.book') }}" class="btn-primary">Book Your First Lesson</a>
    </div>
    @endif

</div>
</x-app-layout>
