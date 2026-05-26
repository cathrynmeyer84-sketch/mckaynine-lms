<x-app-layout :title="'Instructor Dashboard'">
<div class="page-header">
    <h1 class="page-title">My Dashboard</h1>
    @if($instructor)
    <p class="page-subtitle">Welcome back, {{ $instructor->first_name }}.</p>
    @endif
</div>
<div class="page-content">
    @if(!$instructor)
    <div class="card text-center py-8">
        <p class="text-gray-500">Your instructor profile hasn't been set up yet. Please contact admin.</p>
    </div>
    @else

    @php $totalPrivateLessons = $pendingLessons->count() + $upcomingLessons->count(); @endphp

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <a href="#" onclick="smoothScroll(event,'section-classes')" class="stat-card cursor-pointer hover:shadow-md transition-shadow">
            <div class="stat-icon bg-navy/10 text-navy"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            <div><p class="stat-value">{{ $classes->count() }}</p><p class="stat-label">Active Classes</p></div>
        </a>
        <a href="#" onclick="smoothScroll(event,'section-sessions')" class="stat-card cursor-pointer hover:shadow-md transition-shadow">
            <div class="stat-icon bg-brand/10 text-brand"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            <div><p class="stat-value">{{ $upcomingDates->count() }}</p><p class="stat-label">Upcoming Sessions</p></div>
        </a>
        <a href="#" onclick="smoothScroll(event,'section-registers')" class="stat-card cursor-pointer hover:shadow-md transition-shadow {{ $pendingRegisters->count() > 0 ? 'border border-amber/30' : '' }}">
            <div class="stat-icon bg-amber/10 text-amber"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
            <div><p class="stat-value">{{ $pendingRegisters->count() }}</p><p class="stat-label">Pending Registers</p></div>
        </a>
        <a href="#" onclick="smoothScroll(event,'section-private-lessons')" class="stat-card cursor-pointer hover:shadow-md transition-shadow {{ $totalPrivateLessons > 0 ? 'border border-amber/30' : '' }}">
            <div class="stat-icon bg-amber/10 text-amber"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <div><p class="stat-value">{{ $totalPrivateLessons }}</p><p class="stat-label">Private Lessons</p></div>
        </a>
    </div>

    {{-- Pending registers --}}
    @if($pendingRegisters->count() > 0)
    <div id="section-registers" class="card border border-amber/30">
        <h3 class="font-semibold text-amber mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            Registers to Complete
        </h3>
        <div class="space-y-2">
            @foreach($pendingRegisters as $date)
            <div class="flex items-center justify-between p-3 bg-amber/5 rounded-xl border border-amber/20">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $date->dogClass->name }}</p>
                    <p class="text-xs text-gray-500">{{ $date->date->format('D, d M Y') }}</p>
                </div>
                <a href="{{ route('instructor.register.show', [$date->class_id, $date->id]) }}" class="btn-amber btn-sm">Complete</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- My Classes --}}
    <div id="section-classes">
        <h2 class="text-base font-semibold text-navy mb-3">My Classes</h2>
        @if($classes->isEmpty())
        <div class="empty-state"><div class="empty-state-icon"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><p class="text-gray-500">No classes assigned yet.</p></div>
        @else
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($classes as $class)
            <a href="{{ route('instructor.classes.show', $class) }}" class="card card-hover block">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-navy">{{ $class->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $class->classType?->name ?? '' }}</p>
                    </div>
                    <span class="badge badge-{{ $class->status }}">{{ ucfirst($class->status) }}</span>
                </div>
                <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                    <span>{{ $class->confirmedEnrolments->count() }} enrolled</span>
                    @if($class->start_date)<span>Starts {{ $class->start_date->format('d M') }}</span>@endif
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Upcoming sessions --}}
    <div id="section-sessions">
        @if($upcomingDates->count() > 0)
        <h2 class="text-base font-semibold text-navy mb-3">Upcoming Sessions</h2>
        <div class="card divide-y divide-gray-100">
            @foreach($upcomingDates as $date)
            <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $date->dogClass->name }}</p>
                    <p class="text-xs text-gray-500">{{ $date->date->format('D, d M Y') }} @if($date->start_time)at {{ \Carbon\Carbon::parse($date->start_time)->format('H:i') }}@endif</p>
                </div>
                <a href="{{ route('instructor.classes.show', $date->class_id) }}" class="btn-outline btn-sm">View</a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Private lessons (pending + upcoming) --}}
    @if($pendingLessons->count() > 0 || $upcomingLessons->count() > 0)
    <div id="section-private-lessons">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-navy">Private Lessons</h2>
            <a href="{{ route('instructor.private-lessons.index') }}" class="text-sm text-brand hover:underline">Manage</a>
        </div>

        @if($pendingLessons->count() > 0)
        <div class="card divide-y divide-gray-100 mb-3 border border-amber/30">
            <div class="pb-3">
                <p class="text-xs font-semibold text-amber uppercase tracking-wide">Pending — action required</p>
            </div>
            @foreach($pendingLessons as $lesson)
            <div class="flex items-center justify-between py-3 last:pb-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $lesson->handler?->full_name }} · {{ $lesson->dog?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $lesson->requested_date?->format('D, d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</p>
                </div>
                <a href="{{ route('instructor.private-lessons.index', ['tab' => 'pending']) }}" class="btn-amber btn-sm">Respond</a>
            </div>
            @endforeach
        </div>
        @endif

        @if($upcomingLessons->count() > 0)
        <div class="card divide-y divide-gray-100">
            <div class="pb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Confirmed &amp; upcoming</p>
            </div>
            @foreach($upcomingLessons as $lesson)
            <div class="flex items-center justify-between py-3 last:pb-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $lesson->handler?->full_name }} · {{ $lesson->dog?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $lesson->requested_date?->format('D, d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</p>
                </div>
                <span class="badge badge-confirmed">Confirmed</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    @endif
</div>

<script>
function smoothScroll(e, id) {
    e.preventDefault();
    const el = document.getElementById(id);
    if (!el) return;
    // The layout uses overflow-y-auto on <main>, not window
    const container = document.querySelector('main');
    const offset = 16;
    const top = el.getBoundingClientRect().top - container.getBoundingClientRect().top + container.scrollTop - offset;
    container.scrollTo({ top, behavior: 'smooth' });
}
</script>
</x-app-layout>
