<x-app-layout title="Private Lesson">
<div class="page-content">

    <div class="page-header">
        <div>
            <a href="{{ route('admin.private-lessons.index') }}" class="text-sm text-gray-400 hover:text-navy flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Private Lessons
            </a>
            <h1 class="page-title">Lesson — {{ $lesson->dog?->name }}</h1>
        </div>
        <span class="badge {{ $lesson->status_badge_class }} text-sm">{{ $lesson->status_label }}</span>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="card">
            <h2 class="card-title mb-4">Lesson Details</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Handler</dt>
                    <dd class="font-medium text-gray-900">{{ $lesson->handler?->full_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Dog</dt>
                    <dd class="font-medium text-gray-900">{{ $lesson->dog?->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Instructor</dt>
                    <dd class="font-medium text-gray-900">{{ $lesson->instructor?->full_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Requested Date</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $lesson->requested_date?->format('d M Y') ?? '—' }}
                        @if($lesson->requested_start_time)
                        at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}
                        @endif
                    </dd>
                </div>
                @if($lesson->confirmed_date)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Confirmed Date</dt>
                    <dd class="font-medium text-gray-900">
                        {{ $lesson->confirmed_date->format('d M Y') }}
                        @if($lesson->confirmed_start_time)
                        at {{ \Carbon\Carbon::parse($lesson->confirmed_start_time)->format('g:i A') }}
                        @endif
                    </dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Fee</dt>
                    <dd class="font-medium text-gray-900">
                        @if($lesson->fee !== null)
                        R {{ number_format($lesson->fee, 2) }}
                        @else
                        <span class="text-gray-400">Not set</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Booked</dt>
                    <dd class="text-gray-600">{{ $lesson->created_at->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="space-y-4">
            @if($lesson->handler_notes)
            <div class="card">
                <h2 class="card-title mb-2">Handler Notes</h2>
                <p class="text-sm text-gray-700">{{ $lesson->handler_notes }}</p>
            </div>
            @endif

            @if($lesson->instructor_notes)
            <div class="card">
                <h2 class="card-title mb-2">Session Notes</h2>
                <p class="text-sm text-gray-700">{{ $lesson->instructor_notes }}</p>
            </div>
            @endif

            @if($lesson->reschedule_note)
            <div class="card border-l-4 border-amber">
                <h2 class="card-title mb-2 text-amber">Reschedule / Cancellation Note</h2>
                <p class="text-sm text-gray-700">{{ $lesson->reschedule_note }}</p>
            </div>
            @endif
        </div>
    </div>

</div>
</x-app-layout>
