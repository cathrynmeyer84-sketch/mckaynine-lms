<x-app-layout :title="$class->name">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.classes.index') }}" class="text-gray-400 hover:text-navy"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div><h1 class="page-title">{{ $class->name }}</h1><p class="page-subtitle">{{ $class->classType?->name ?? '' }}</p></div>
    </div>
</div>

<div class="page-content">
<div x-data="{ tab: 'dogs' }">

    {{-- Tab nav --}}
    <div class="flex border-b border-gray-200 mb-4 overflow-x-auto">
        <button @click="tab='dogs'" :class="tab==='dogs' ? 'border-brand text-brand' : 'border-transparent text-gray-500'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Dogs ({{ $class->confirmedEnrolments->count() }})</button>
        <button @click="tab='register'" :class="tab==='register' ? 'border-brand text-brand' : 'border-transparent text-gray-500'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Register</button>
        <button @click="tab='content'" :class="tab==='content' ? 'border-brand text-brand' : 'border-transparent text-gray-500'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Content</button>
        <button @click="tab='briefing'" :class="tab==='briefing' ? 'border-brand text-brand' : 'border-transparent text-gray-500'" class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">Briefing</button>
        @if($class->classType?->has_grading)
        <a href="{{ route('instructor.grade.index', $class) }}" class="px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 whitespace-nowrap hover:text-brand hover:border-brand transition-colors">Grading</a>
        @endif
    </div>

    {{-- ── DOGS TAB ── --}}
    <div x-show="tab==='dogs'" class="space-y-3">
        @forelse($class->confirmedEnrolments as $enrolment)
        @php $dog = $enrolment->dog; $handler = $enrolment->handler; @endphp
        <div class="card cursor-pointer hover:bg-gray-50 transition-colors" x-data="{ goalOpen: false }"
            @click="window.location='{{ route('instructor.classes.dog', [$class, $enrolment]) }}'">

            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand/10 flex items-center justify-center shrink-0 font-bold text-brand text-sm">
                        {{ strtoupper(substr($dog->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $dog->name }}
                            @if($dog->gender) <span class="text-xs text-gray-400 font-normal">({{ ucfirst($dog->gender) }})</span>@endif
                        </p>
                        <p class="text-sm text-gray-500">{{ $dog->breed ? $dog->breed . ' · ' : '' }}{{ $handler->full_name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($dog->behaviour_problems_details)
                    <span class="w-2 h-2 rounded-full bg-amber-400" title="Behaviour note"></span>
                    @endif
                    @if($dog->health_issues)
                    <span class="w-2 h-2 rounded-full bg-red-400" title="Health note"></span>
                    @endif
                    <button @click.stop="goalOpen = true" class="btn btn-outline btn-sm">+ Add Goal</button>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>

            {{-- Add Goal Modal --}}
            <div x-show="goalOpen" x-cloak @click.stop="goalOpen = false"
                style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:50;display:flex;align-items:center;justify-content:center;">
                <div @click.stop class="bg-white rounded-2xl p-6 w-full max-w-md mx-4 shadow-xl">
                    <h3 class="font-semibold text-navy mb-4">Add Goal — {{ $dog->name }}</h3>
                    <form method="POST" action="{{ route('instructor.classes.goals.store', $class) }}" class="space-y-3">
                        @csrf
                        <input type="hidden" name="enrolment_id" value="{{ $enrolment->id }}">
                        <div>
                            <label class="form-label">Goal</label>
                            <textarea name="goal" class="form-textarea mt-1" rows="3" required placeholder="e.g. Improve loose-lead walking..."></textarea>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="visible_to_handler" class="rounded border-gray-300 text-brand">
                            Visible to handler in their portal
                        </label>
                        <div class="flex gap-2 pt-1">
                            <button type="submit" class="btn btn-primary flex-1">Save Goal</button>
                            <button type="button" @click.stop="goalOpen = false" class="btn btn-outline">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        @empty
        <div class="empty-state"><p class="text-gray-500 text-sm">No dogs enrolled yet.</p></div>
        @endforelse
    </div>

    {{-- ── REGISTER TAB ── --}}
    <div x-show="tab==='register'" class="space-y-2">
        @php $activeDates = $class->classDates->where('is_off_week', false); @endphp
        @forelse($activeDates as $date)
        <a href="{{ route('instructor.register.show', [$class, $date]) }}"
            class="card flex items-center justify-between gap-3 hover:bg-gray-50 transition-colors cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-gray-500">W{{ $date->week_number }}</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $date->date->format('D, d M Y') }}</p>
                    @if($date->start_time)<p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($date->start_time)->format('H:i') }}</p>@endif
                </div>
            </div>
            @if($date->registers_count > 0)
                <span class="badge badge-active text-xs">Done</span>
            @elseif($date->date->isPast())
                <span class="badge badge-pending text-xs">Pending</span>
            @else
                <span class="text-xs text-gray-400">Upcoming</span>
            @endif
        </a>
        @empty
        <p class="text-sm text-gray-400 text-center py-8">No class dates found.</p>
        @endforelse
    </div>

    {{-- ── CONTENT TAB ── --}}
    <div x-show="tab==='content'" class="space-y-2">
        @php $activeDates = $class->classDates->where('is_off_week', false); @endphp
        @forelse($activeDates as $date)
        @php
            $wc  = $date->weeklyContent;
            $ctw = $date->classTypeWeek;
            $rowTitle = $wc?->title ?: $ctw?->title;
            $hasContent = $wc || $ctw?->title || $ctw?->description || $ctw?->youtube_url || $ctw?->practice_checklist || $ctw?->what_to_bring_next_week;
        @endphp
        <a href="{{ route('instructor.classes.week-content', [$class, $date]) }}"
            class="card flex items-center justify-between gap-3 hover:bg-gray-50 transition-colors cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-gray-500">W{{ $date->week_number }}</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $date->date->format('D, d M Y') }}</p>
                    @if($rowTitle)<p class="text-xs text-gray-400">{{ $rowTitle }}</p>@endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if(!$hasContent)
                    <span class="badge badge-pending text-xs">No content</span>
                @elseif($wc?->is_published)
                    <span class="badge badge-active text-xs">Published</span>
                @else
                    <span class="badge badge-pending text-xs">Template</span>
                @endif
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @empty
        <p class="text-sm text-gray-400 text-center py-8">No class dates found.</p>
        @endforelse
    </div>

    {{-- ── BRIEFING TAB ── --}}
    <div x-show="tab==='briefing'" class="space-y-2">
        @php $activeDates = $class->classDates->where('is_off_week', false); @endphp
        @forelse($activeDates as $date)
        @php $itemCount = $date->classTypeWeek?->briefingItems->count() ?? 0; @endphp
        <a href="{{ route('instructor.classes.week-briefing', [$class, $date]) }}"
            class="card flex items-center justify-between gap-3 hover:bg-gray-50 transition-colors cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-gray-500">W{{ $date->week_number }}</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">{{ $date->date->format('D, d M Y') }}</p>
                    @if($date->classTypeWeek?->title)
                    <p class="text-xs text-gray-400">{{ $date->classTypeWeek->title }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($itemCount > 0)
                    <span class="badge badge-active text-xs">{{ $itemCount }} {{ Str::plural('exercise', $itemCount) }}</span>
                @else
                    <span class="badge badge-pending text-xs">No briefing</span>
                @endif
                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @empty
        <p class="text-sm text-gray-400 text-center py-8">No class dates found.</p>
        @endforelse
    </div>


</div>
</div>
</x-app-layout>
