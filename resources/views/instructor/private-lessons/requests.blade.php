<x-app-layout title="Private Lesson Requests">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Private Lesson Requests</h1>
            <p class="page-subtitle">Manage incoming and upcoming private sessions</p>
        </div>
        <a href="{{ route('instructor.private-lessons.availability') }}" class="btn-outline">Availability Settings</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div x-data="{ tab: '{{ $pending->count() > 0 ? 'pending' : ($upcoming->count() > 0 ? 'upcoming' : 'past') }}' }">

        {{-- Tabs --}}
        <div class="flex gap-1 border-b border-gray-200 mb-6">
            <button @click="tab = 'pending'"
                :class="tab === 'pending' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors">
                Pending
                @if($pending->count() > 0)
                <span class="ml-1.5 bg-amber text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $pending->count() }}</span>
                @endif
            </button>
            <button @click="tab = 'upcoming'"
                :class="tab === 'upcoming' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors">
                Upcoming
            </button>
            <button @click="tab = 'past'"
                :class="tab === 'past' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors">
                Past
            </button>
        </div>

        {{-- Pending --}}
        <div x-show="tab === 'pending'" x-cloak>
            @forelse($pending as $lesson)
            <div class="card mb-4" x-data="{ rescheduleOpen: false, rejectOpen: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-semibold text-navy">{{ $lesson->handler?->full_name }}</p>
                        <p class="text-sm text-gray-500">Dog: <span class="font-medium text-gray-700">{{ $lesson->dog?->name }}</span></p>
                    </div>
                    <span class="badge badge-pending shrink-0">Pending</span>
                </div>
                <div class="text-sm text-gray-600 mb-4">
                    <p>Requested: <span class="font-medium text-gray-800">{{ $lesson->requested_date?->format('l, d M Y') }}</span>
                        at <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</span>
                    </p>
                    @if($lesson->handler_notes)
                    <p class="mt-2 p-3 bg-gray-50 rounded-xl text-gray-700 italic">"{{ $lesson->handler_notes }}"</p>
                    @endif
                </div>

                <div class="flex gap-2 flex-wrap">
                    {{-- Confirm --}}
                    <form method="POST" action="{{ route('instructor.private-lessons.confirm', $lesson) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Confirm</button>
                    </form>

                    {{-- Request Reschedule --}}
                    <button type="button" @click="rescheduleOpen = true" class="btn-outline btn-sm">Request Reschedule</button>

                    {{-- Reject --}}
                    <button type="button" @click="rejectOpen = true" class="btn-outline btn-sm text-red-500 border-red-200 hover:bg-red-50">Reject</button>
                </div>

                {{-- Reschedule modal --}}
                <div x-show="rescheduleOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    @click.self="rescheduleOpen = false">
                    <div class="card w-full max-w-md" @click.stop>
                        <h3 class="font-semibold text-navy mb-3">Request Reschedule</h3>
                        <p class="text-sm text-gray-500 mb-4">Let {{ $lesson->handler?->first_name }} know why you'd like to reschedule.</p>
                        <form method="POST" action="{{ route('instructor.private-lessons.reschedule', $lesson) }}">
                            @csrf
                            <textarea name="reschedule_note" rows="3" class="input mb-4"
                                placeholder="e.g. I'm unavailable that day — please re-book for another slot." required maxlength="500"></textarea>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="rescheduleOpen = false" class="btn-outline btn-sm">Cancel</button>
                                <button type="submit" class="btn-primary btn-sm">Send Request</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Reject modal --}}
                <div x-show="rejectOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    @click.self="rejectOpen = false">
                    <div class="card w-full max-w-md" @click.stop>
                        <h3 class="font-semibold text-navy mb-3">Reject Lesson Request</h3>
                        <p class="text-sm text-gray-500 mb-4">Optionally add a note to {{ $lesson->handler?->first_name }}.</p>
                        <form method="POST" action="{{ route('instructor.private-lessons.reject', $lesson) }}">
                            @csrf
                            <textarea name="reason" rows="2" class="input mb-4"
                                placeholder="Optional reason…" maxlength="500"></textarea>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="rejectOpen = false" class="btn-outline btn-sm">Cancel</button>
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-xl px-4 py-2 text-sm font-medium transition-colors">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="card text-center py-12">
                <p class="text-gray-400">No pending requests.</p>
            </div>
            @endforelse
        </div>

        {{-- Upcoming --}}
        <div x-show="tab === 'upcoming'" x-cloak>
            @forelse($upcoming as $lesson)
            <div class="card mb-4" x-data="{ completeOpen: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-semibold text-navy">{{ $lesson->handler?->full_name }}</p>
                        <p class="text-sm text-gray-500">Dog: <span class="font-medium text-gray-700">{{ $lesson->dog?->name }}</span></p>
                    </div>
                    <span class="badge badge-confirmed shrink-0">Confirmed</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    <span class="font-medium text-gray-800">{{ $lesson->requested_date?->format('l, d M Y') }}</span>
                    at <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</span>
                </p>

                <button type="button" @click="completeOpen = true" class="btn-primary btn-sm">Mark Complete</button>

                {{-- Complete modal --}}
                <div x-show="completeOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    @click.self="completeOpen = false">
                    <div class="card w-full max-w-md" @click.stop>
                        <h3 class="font-semibold text-navy mb-3">Complete Lesson — {{ $lesson->dog?->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Add session notes to send to the handler (optional).</p>
                        <form method="POST" action="{{ route('instructor.private-lessons.complete', $lesson) }}">
                            @csrf
                            <textarea name="instructor_notes" rows="4" class="input mb-4"
                                placeholder="What did you work on? Any homework or follow-up notes for the handler…" maxlength="2000"></textarea>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="completeOpen = false" class="btn-outline btn-sm">Cancel</button>
                                <button type="submit" class="btn-primary btn-sm">Complete &amp; Send Notes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="card text-center py-12">
                <p class="text-gray-400">No upcoming confirmed lessons.</p>
            </div>
            @endforelse
        </div>

        {{-- Past --}}
        <div x-show="tab === 'past'" x-cloak>
            @forelse($past as $lesson)
            <div class="card mb-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-medium text-gray-800">{{ $lesson->handler?->full_name }}</p>
                            <span class="text-gray-300">·</span>
                            <p class="text-sm text-gray-500">{{ $lesson->dog?->name }}</p>
                        </div>
                        <p class="text-xs text-gray-400">{{ $lesson->requested_date?->format('d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</p>
                        @if($lesson->instructor_notes)
                        <p class="text-xs text-gray-500 mt-2 italic">"{{ \Str::limit($lesson->instructor_notes, 100) }}"</p>
                        @endif
                    </div>
                    <span class="badge {{ $lesson->status_badge_class }} shrink-0">{{ $lesson->status_label }}</span>
                </div>
            </div>
            @empty
            <div class="card text-center py-12">
                <p class="text-gray-400">No past lessons yet.</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</x-app-layout>
