<x-app-layout :title="'Admin Dashboard'">
<div class="page-content">

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="page-subtitle">Overview of McKaynine LMS activity</p>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <div class="stat-icon bg-brand/10">
                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="stat-value">{{ $stats['total_handlers'] }}</div>
            <div class="stat-label">Active Handlers</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-amber/10">
                <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="stat-value">{{ $stats['pending_enrolments'] }}</div>
            <div class="stat-label">Pending Enrolments</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-amber/10">
                <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="stat-value">{{ $stats['pending_assessments'] }}</div>
            <div class="stat-label">Pending Assessments</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bg-green-50">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="stat-value">{{ $stats['active_classes'] }}</div>
            <div class="stat-label">Active Classes</div>
        </div>
    </div>

    {{-- Row 1: New Enrolments | Class Confirmations | Release Results --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- New Enrolments --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-navy">New Enrolments</h3>
                @if($pendingEnrolments->count())
                <span class="badge badge-pending">{{ $pendingEnrolments->count() }}</span>
                @endif
            </div>
            @if($pendingEnrolments->count())
            <div class="space-y-3">
                @foreach($pendingEnrolments as $enrolment)
                <a href="{{ route('admin.enrolments.show', $enrolment) }}"
                    class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $enrolment->dog?->name }} &middot; {{ $enrolment->dogClass?->name ?? 'No class yet' }}</p>
                        <p class="text-xs text-gray-400">{{ $enrolment->enrolled_at?->diffForHumans() }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.enrolments.index') }}" class="text-sm text-brand hover:underline font-medium">View all enrolments &rarr;</a>
            </div>
            @else
            <div class="empty-state py-6">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No new enrolments</p>
            </div>
            @endif
        </div>

        {{-- Class Confirmations --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-navy">Class Confirmations</h3>
                @if($pendingClassConfirmations->count())
                <span class="badge badge-pending">{{ $pendingClassConfirmations->count() }}</span>
                @endif
            </div>
            @if($pendingClassConfirmations->count())
            <div class="space-y-3">
                @foreach($pendingClassConfirmations as $enrolment)
                <a href="{{ route('admin.enrolments.show', $enrolment) }}"
                    class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0 hover:bg-gray-50 -mx-2 px-2 rounded-lg transition-colors">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $enrolment->dog?->name }} &middot; {{ $enrolment->dogClass?->name ?? 'No class yet' }}</p>
                        <p class="text-xs text-gray-400">{{ $enrolment->enrolled_at?->diffForHumans() }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.enrolments.index') }}" class="text-sm text-brand hover:underline font-medium">View all enrolments &rarr;</a>
            </div>
            @else
            <div class="empty-state py-6">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No class confirmations pending</p>
            </div>
            @endif
        </div>

        {{-- Results to Release --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-navy">Results to Release</h3>
                @if($pendingResults->count())
                <span class="badge badge-pending">{{ $pendingResults->count() }}</span>
                @endif
            </div>
            @if($pendingResults->count())
            <div class="space-y-3">
                @foreach($pendingResults as $result)
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $result->enrolment?->handler?->first_name }} {{ $result->enrolment?->handler?->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $result->enrolment?->dog?->name }} &middot; {{ $result->enrolment?->dogClass?->name }}</p>
                        <p class="text-xs text-gray-400">Score: {{ $result->score }}%@if($result->achievement_level) &middot; {{ ucfirst($result->achievement_level) }}@endif</p>
                    </div>
                    <form method="POST" action="{{ route('admin.results.release', $result) }}">
                        @csrf
                        <button type="submit" class="btn-amber btn-sm flex-shrink-0">Release</button>
                    </form>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.results.index') }}" class="text-sm text-brand hover:underline font-medium">All results &rarr;</a>
            </div>
            @else
            <div class="empty-state py-6">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No results awaiting release</p>
            </div>
            @endif
        </div>

    </div>

    {{-- Row 2: Pending Assessments | Upcoming Assessments --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Pending Assessments --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-navy">Pending Assessments</h3>
                @if($pendingAssessments->count())
                <span class="badge badge-pending">{{ $pendingAssessments->count() }}</span>
                @endif
            </div>
            @if($pendingAssessments->count())
            <div class="space-y-3">
                @foreach($pendingAssessments as $assessment)
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $assessment->handler?->first_name }} {{ $assessment->handler?->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $assessment->dog?->name }}</p>
                        <p class="text-xs text-gray-400">{{ $assessment->created_at?->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm flex-shrink-0">View</a>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.assessments.index') }}" class="text-sm text-brand hover:underline font-medium">Manage assessments &rarr;</a>
            </div>
            @else
            <div class="empty-state py-6">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No pending assessments</p>
            </div>
            @endif
        </div>

        {{-- Upcoming Assessments --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-navy">Upcoming Assessments</h3>
                <div class="flex items-center gap-3">
                    @if($upcomingAssessments->count())
                    <span class="badge badge-active">{{ $upcomingAssessments->count() }}</span>
                    @endif
                    <a href="{{ route('admin.assessments.index') }}" class="text-sm text-brand hover:underline font-medium">View all &rarr;</a>
                </div>
            </div>
            @if($upcomingAssessments->count())
            <div class="space-y-3">
                @foreach($upcomingAssessments as $assessment)
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $assessment->handler?->first_name }} {{ $assessment->handler?->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $assessment->dog?->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $assessment->slot->date->format('d M Y') }} &middot;
                            {{ \Carbon\Carbon::parse($assessment->slot->start_time)->format('g:i A') }}
                            @if($assessment->slot->end_time)– {{ \Carbon\Carbon::parse($assessment->slot->end_time)->format('g:i A') }}@endif
                        </p>
                    </div>
                    <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm flex-shrink-0">View</a>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state py-6">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No assessments booked yet</p>
            </div>
            @endif
        </div>

    </div>

    {{-- Classes Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Upcoming Class Starts --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-navy">Upcoming Classes</h2>
                <a href="{{ route('admin.classes.create') }}" class="btn-primary btn-sm">+ New Class</a>
            </div>
            @if($upcomingClasses->count())
            <div class="space-y-3">
                @foreach($upcomingClasses as $class)
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $class->name }}</p>
                        <p class="text-xs text-gray-500">
                            @if($class->instructors->first())
                                {{ $class->instructors->first()->first_name }} {{ $class->instructors->first()->last_name }}
                                &middot;
                            @endif
                            Starts {{ $class->start_date?->format('d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $class->dates->count() }} scheduled sessions</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-pending">Upcoming</span>
                        <a href="{{ route('admin.classes.show', $class) }}" class="text-brand hover:text-navy">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No upcoming classes scheduled</p>
                <a href="{{ route('admin.classes.create') }}" class="btn-primary btn-sm mt-3">Create a class</a>
            </div>
            @endif
        </div>

        {{-- Active Classes --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-navy">Active Classes</h2>
                <a href="{{ route('admin.classes.index') }}" class="text-sm text-brand hover:underline font-medium">View all</a>
            </div>
            @if($activeClasses->count())
            <div class="space-y-3">
                @foreach($activeClasses as $class)
                <div class="flex items-start justify-between gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $class->name }}</p>
                        <p class="text-xs text-gray-500">
                            @if($class->instructors->first())
                                {{ $class->instructors->first()->first_name }} {{ $class->instructors->first()->last_name }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $class->confirmedEnrolments->count() }} enrolled
                            @if($class->max_capacity)/ {{ $class->max_capacity }} max @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-active">Active</span>
                        <a href="{{ route('admin.classes.show', $class) }}" class="text-brand hover:text-navy">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-sm text-gray-500">No active classes</p>
            </div>
            @endif
        </div>

    </div>

    {{-- Private Lessons --}}
    @if($pendingLessons->count() > 0 || $upcomingLessons->count() > 0)
    <div class="mt-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-navy">Private Lessons</h2>
            <a href="{{ route('admin.private-lessons.index') }}" class="text-sm text-brand hover:underline">View all</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Pending --}}
            <div>
                <p class="text-xs font-semibold text-amber uppercase tracking-wide mb-2">Pending — awaiting instructor response</p>
                @if($pendingLessons->isEmpty())
                <div class="card text-center py-6">
                    <p class="text-sm text-gray-400">No pending lessons</p>
                </div>
                @else
                <div class="card divide-y divide-gray-100 border border-amber/30">
                    @foreach($pendingLessons as $lesson)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $lesson->handler?->full_name }} · {{ $lesson->dog?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $lesson->requested_date?->format('D, d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</p>
                            @if($lesson->instructor)<p class="text-xs text-gray-400">{{ $lesson->instructor->first_name }} {{ $lesson->instructor->last_name }}</p>@endif
                        </div>
                        <a href="{{ route('admin.private-lessons.show', $lesson) }}" class="btn-outline btn-sm">View</a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Upcoming confirmed --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Confirmed &amp; upcoming</p>
                @if($upcomingLessons->isEmpty())
                <div class="card text-center py-6">
                    <p class="text-sm text-gray-400">No upcoming lessons</p>
                </div>
                @else
                <div class="card divide-y divide-gray-100">
                    @foreach($upcomingLessons as $lesson)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $lesson->handler?->full_name }} · {{ $lesson->dog?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $lesson->requested_date?->format('D, d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</p>
                            @if($lesson->instructor)<p class="text-xs text-gray-400">{{ $lesson->instructor->first_name }} {{ $lesson->instructor->last_name }}</p>@endif
                        </div>
                        <span class="badge badge-confirmed">Confirmed</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
    @endif

</div>
</x-app-layout>
