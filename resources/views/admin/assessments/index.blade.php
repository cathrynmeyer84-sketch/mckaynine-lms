<x-app-layout :title="'Assessments'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Assessments</h1>
            <p class="page-subtitle">Manage intake assessments</p>
        </div>
        <a href="{{ route('admin.assessments.settings') }}" class="btn btn-outline btn-sm">Settings</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Pending --}}
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-navy">Pending Requests</h2>
                    @if($pending->count())
                        <span class="badge badge-pending">{{ $pending->count() }}</span>
                    @endif
                </div>

                @if($pending->count())
                <div class="space-y-3">
                    @foreach($pending as $assessment)
                    <div class="flex items-start justify-between gap-3 p-4 bg-amber/5 border border-amber/20 rounded-xl">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">{{ $assessment->handler?->first_name }} {{ $assessment->handler?->last_name }}</p>
                            <p class="text-sm text-gray-600">Dog: {{ $assessment->dog?->name }}
                                @if($assessment->dog?->breed) &middot; {{ $assessment->dog->breed }}@endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">Requested {{ $assessment->created_at?->diffForHumans() }}</p>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm">View</a>
                            <form method="POST" action="{{ route('admin.assessments.send-booking-link', $assessment) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Send Booking Link</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state py-8">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500">No pending assessment requests</p>
                </div>
                @endif
            </div>

            {{-- Booking Link Sent --}}
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-navy">Booking Link Sent</h2>
                    @if($offered->count())
                        <span class="badge badge-amber">{{ $offered->count() }}</span>
                    @endif
                </div>
                @if($offered->count())
                <div class="space-y-3">
                    @foreach($offered as $assessment)
                    <div class="flex items-start justify-between gap-3 p-4 bg-amber/5 border border-amber/20 rounded-xl">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">{{ $assessment->handler?->first_name }} {{ $assessment->handler?->last_name }}</p>
                            <p class="text-sm text-gray-600">Dog: {{ $assessment->dog?->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">Link sent {{ $assessment->updated_at?->diffForHumans() }} · awaiting booking</p>
                        </div>
                        <div class="flex gap-2 flex-shrink-0">
                            <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm">View</a>
                            <form method="POST" action="{{ route('admin.assessments.send-booking-link', $assessment) }}">
                                @csrf
                                <button type="submit" class="btn-amber btn-sm">Resend</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state py-6">
                    <p class="text-sm text-gray-500">No pending booking invitations</p>
                </div>
                @endif
            </div>

            {{-- Booked --}}
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-navy">Booked Assessments</h2>
                    @if($booked->count())
                        <span class="badge badge-active">{{ $booked->count() }}</span>
                    @endif
                </div>

                @if($booked->count())
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Handler</th>
                                <th>Dog</th>
                                <th>Slot</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booked as $assessment)
                            <tr>
                                <td class="font-medium text-gray-900">{{ $assessment->handler?->first_name }} {{ $assessment->handler?->last_name }}</td>
                                <td class="text-sm text-gray-600">{{ $assessment->dog?->name }}</td>
                                <td class="text-sm text-gray-600">
                                    @if($assessment->slot)
                                        {{ \Carbon\Carbon::parse($assessment->slot->date)->format('d M Y') }}
                                        @if($assessment->slot->start_time)
                                            at {{ \Carbon\Carbon::parse($assessment->slot->start_time)->format('g:i A') }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm">View</a>
                                        <a href="{{ route('admin.assessments.score', $assessment) }}" class="btn-primary btn-sm">Score</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state py-6">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500">No booked assessments</p>
                </div>
                @endif
            </div>

            {{-- Completed assessments --}}
            <div class="card" x-data="{ open: false }">
                <button type="button" @click="open = !open"
                    class="flex items-center justify-between w-full text-left">
                    <div class="flex items-center gap-3">
                        <h2 class="font-semibold text-navy">Completed Assessments</h2>
                        @if($completed->flatten()->count())
                            <span class="badge badge-completed">{{ $completed->flatten()->count() }}</span>
                        @endif
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-cloak class="mt-4 space-y-6">
                    @forelse($completed as $month => $assessments)
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">{{ $month }}</h3>
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Handler</th>
                                        <th>Dog</th>
                                        <th>Outcome</th>
                                        <th>Enrolled</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessments as $assessment)
                                    @php
                                        $score     = $assessment->scores;
                                        $outcome   = $score?->final_outcome ?? $score?->recommended_outcome;
                                        $enrolments = $assessment->handler?->enrolments ?? collect();
                                        $confirmed  = $enrolments->where('status', 'confirmed')->isNotEmpty();
                                        $pending    = !$confirmed && $enrolments->isNotEmpty();
                                    @endphp
                                    <tr>
                                        <td class="font-medium text-gray-900">{{ $assessment->handler?->first_name }} {{ $assessment->handler?->last_name }}</td>
                                        <td class="text-sm text-gray-600">{{ $assessment->dog?->name }}</td>
                                        <td>
                                            @if($outcome === 'group_class')
                                                <span class="badge badge-active">Group Class</span>
                                            @elseif($outcome === 'private_lessons')
                                                <span class="badge badge-amber">Private Lessons</span>
                                            @elseif($outcome === 'behaviourist')
                                                <span class="badge" style="background:#fee2e2;color:#991b1b;">Behaviourist</span>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($outcome === 'behaviourist')
                                                <span class="text-gray-400 text-xs">N/A</span>
                                            @elseif($confirmed)
                                                <span class="text-green-600 text-xs font-medium">✓ Confirmed</span>
                                            @elseif($pending)
                                                <span class="text-brand text-xs font-medium">⏳ Pending confirmation</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-amber text-xs font-medium">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    Follow up
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 py-4">No completed assessments yet.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="card">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-navy">Assessment Slots</h2>
                    @if($slots->count())
                    <span class="text-xs text-gray-400">{{ $slots->count() }} upcoming</span>
                    @endif

                </div>
                @if($slots->count())
                <div class="space-y-2 mb-4">
                    @foreach($slots->take(4) as $slot)
                    <div class="p-3 bg-gray-50 rounded-xl text-sm">
                        <p class="font-medium text-gray-900">{{ $slot->date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                            · {{ $slot->remaining }} spot{{ $slot->remaining === 1 ? '' : 's' }} open
                        </p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 mb-4">No upcoming slots — <a href="{{ route('admin.assessments.slots') }}" class="text-brand hover:underline">set up availability</a>.</p>
                @endif
                <a href="{{ route('admin.assessments.slots') }}" class="btn btn-outline w-full text-center block text-sm">
                    Manage Slots →
                </a>
            </div>
        </div>
    </div>

</div>
</x-app-layout>
