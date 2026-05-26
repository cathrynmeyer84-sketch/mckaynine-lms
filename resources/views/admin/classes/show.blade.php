<x-app-layout :title="$class->name">
<div class="page-content">

    {{-- Header --}}
    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $class->name }}</h1>
                <p class="page-subtitle">{{ $class->classType?->name ?? '' }}</p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($class->classType?->has_structured_content)
            <a href="{{ route('admin.classes.content-schedule', $class) }}" class="btn-outline">Content Schedule</a>
            @endif
            <a href="{{ route('admin.classes.register', $class) }}" class="btn-outline">View Register</a>
            <a href="{{ route('admin.classes.info-page', $class) }}" class="btn-outline {{ $class->info_page_enabled ? 'border-green-300 text-green-700' : '' }}">Info Page</a>
            @php
                $isTermNoExam = $class->classType?->duration_type === 'term' && !$class->has_final_exam;
                $alreadyComplete = $class->confirmedEnrolments->contains(fn($e) => $e->examResult?->achievement_level === 'completed');
                $canMarkComplete = $isTermNoExam && $class->end_date?->isPast() && !$alreadyComplete;
            @endphp
            @if($canMarkComplete)
            <form method="POST" action="{{ route('admin.classes.mark-complete', $class) }}"
                onsubmit="return confirm('Mark this class as complete? Rosettes will be awarded and completion messages sent to all enrolled handlers.')">
                @csrf
                <button type="submit" class="btn-amber">Mark as Complete</button>
            </form>
            @elseif($alreadyComplete)
            <span class="btn-outline border-green-300 text-green-700 cursor-default">Completed ✓</span>
            @endif
            <a href="{{ route('admin.classes.edit', $class) }}" class="btn-primary">Edit Class</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Class Info --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Class Information</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Status</p>
                        @php
                            $sc = match($class->status) {
                                'active' => 'badge-active',
                                'upcoming' => 'badge-pending',
                                'completed' => 'badge-completed',
                                default => 'badge'
                            };
                        @endphp
                        <span class="badge {{ $sc }}">{{ ucfirst($class->status ?? 'draft') }}</span>
                    </div>

                    @if($class->start_date)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Dates</p>
                        <p class="text-sm text-gray-700">
                            {{ $class->start_date->format('d M Y') }}
                            @if($class->end_date) &rarr; {{ $class->end_date->format('d M Y') }}@endif
                        </p>
                    </div>
                    @endif

                    @if($class->location)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Location</p>
                        <p class="text-sm text-gray-700">{{ $class->location }}</p>
                    </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Enrolled / Capacity</p>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold text-navy text-lg">{{ $class->confirmedEnrolments->count() }}</span>
                            @if($class->max_capacity)
                                <span class="text-gray-400">/ {{ $class->max_capacity }}</span>
                            @endif
                        </p>
                        @if($class->max_capacity)
                        <div class="mt-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                            @php $pct = min(100, round($class->confirmedEnrolments->count() / $class->max_capacity * 100)); @endphp
                            <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-amber' : 'bg-brand') }}" style="width: {{ $pct }}%"></div>
                        </div>
                        @endif
                    </div>

                    @if($class->has_final_exam)
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Has final exam / grading
                    </div>
                    @endif

                    @if($class->description)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Description</p>
                        <p class="text-sm text-gray-700">{{ $class->description }}</p>
                    </div>
                    @endif
                </div>

                {{-- Instructors --}}
                @if($class->instructors->count())
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Instructors</p>
                    <div class="space-y-1">
                        @foreach($class->instructors as $inst)
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <div class="w-6 h-6 rounded-full bg-brand flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-semibold">{{ substr($inst->first_name, 0, 1) }}</span>
                            </div>
                            {{ $inst->first_name }} {{ $inst->last_name }}
                            @if($inst->pivot->is_lead)
                                <span class="text-xs text-amber font-medium">(Lead)</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>


        </div>

        {{-- Right column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Enrolled Handlers --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">
                    Enrolled Handlers &amp; Dogs
                    <span class="text-gray-400 font-normal text-sm ml-1">({{ $class->confirmedEnrolments->count() }})</span>
                </h2>

                @if($class->confirmedEnrolments->count())
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Handler</th>
                                <th>Dog</th>
                                <th>Breed</th>
                                <th>Instructor</th>
                                <th>Confirmed</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->confirmedEnrolments as $enrolment)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.handlers.show', $enrolment->handler_id) }}"
                                        class="font-medium text-brand hover:text-navy">
                                        {{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }}
                                    </a>
                                </td>
                                <td class="text-sm text-gray-700">{{ $enrolment->dog?->name ?? '—' }}</td>
                                <td class="text-sm text-gray-500">{{ $enrolment->dog?->breed ?? '—' }}</td>
                                <td>
                                    @if($class->instructors->count())
                                    <form method="POST" action="{{ route('admin.enrolments.assign-instructor', $enrolment) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <select name="assigned_instructor_id"
                                            onchange="this.form.submit()"
                                            class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 text-gray-700 bg-white focus:outline-none focus:ring-1 focus:ring-brand cursor-pointer">
                                            <option value="">Unassigned</option>
                                            @foreach($class->instructors as $inst)
                                            <option value="{{ $inst->id }}"
                                                {{ $enrolment->assigned_instructor_id == $inst->id ? 'selected' : '' }}>
                                                {{ $inst->first_name }} {{ $inst->last_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    @else
                                    <span class="text-xs text-gray-400">No instructors</span>
                                    @endif
                                </td>
                                <td class="text-sm text-gray-500">{{ $enrolment->confirmed_at?->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.handlers.show', $enrolment->handler_id) }}" class="btn-outline btn-sm">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p>No confirmed enrolments yet</p>
                </div>
                @endif
            </div>

            {{-- Full Schedule --}}
            @if($class->dates->count())
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-navy">Class Schedule</h2>
                    <span class="text-xs text-gray-400">{{ $class->dates->where('is_off_week', false)->count() }} sessions · {{ $class->dates->where('is_off_week', true)->count() }} off</span>
                </div>
                <div class="space-y-2">
                    @foreach($class->dates as $date)
                    @if($date->is_off_week)
                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl bg-gray-50 opacity-60">
                        <div class="w-9 h-9 rounded-lg bg-gray-200 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 line-through">{{ $date->date->format('l, d M Y') }}</p>
                            @if($date->off_week_reason)
                            <p class="text-xs text-gray-400">{{ $date->off_week_reason }}</p>
                            @endif
                        </div>
                        <span class="badge text-xs shrink-0">Off</span>
                    </div>
                    @else
                    <div class="px-3 py-2 rounded-xl {{ $date->date->isToday() ? 'bg-brand/5 ring-1 ring-brand/20' : ($date->date->isPast() ? 'bg-gray-50' : 'bg-white border border-gray-100') }}"
                         x-data="{ open: false }">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg {{ $date->date->isPast() ? 'bg-gray-100' : 'bg-brand/10' }} flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold {{ $date->date->isPast() ? 'text-gray-400' : 'text-brand' }}">W{{ $date->week_number }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium {{ $date->date->isPast() ? 'text-gray-500' : 'text-gray-900' }}">
                                    {{ $date->date->format('l, d M Y') }}
                                    @if($date->date->isToday())<span class="ml-1 text-xs text-brand font-semibold">Today</span>@endif
                                </p>
                                @if($date->start_time)
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($date->start_time)->format('g:i A') }}@if($date->end_time) – {{ \Carbon\Carbon::parse($date->end_time)->format('g:i A') }}@endif</p>
                                @endif
                                @if($date->standInInstructor)
                                <p class="text-xs text-amber font-medium mt-0.5">
                                    Stand-in: {{ $date->standInInstructor->full_name }}
                                </p>
                                @endif
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                @if($allInstructors->count())
                                <button @click="open = !open" type="button"
                                    class="text-xs text-gray-400 hover:text-brand transition-colors"
                                    title="Set stand-in instructor">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </button>
                                @endif
                                @if($date->content_sent_at)
                                    <span class="badge badge-active text-xs">Sent</span>
                                @elseif($date->weeklyContent)
                                    <span class="badge badge-confirmed text-xs">Content ready</span>
                                @elseif(!$date->date->isPast())
                                    <span class="badge text-xs">No content</span>
                                @endif
                            </div>
                        </div>

                        {{-- Stand-in selector (inline, toggled) --}}
                        @if($allInstructors->count())
                        <div x-show="open" x-cloak class="mt-2 pt-2 border-t border-gray-100">
                            <form method="POST" action="{{ route('admin.classes.dates.stand-in', [$class, $date]) }}"
                                  class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <label class="text-xs text-gray-500 whitespace-nowrap">Stand-in instructor:</label>
                                <select name="stand_in_instructor_id" class="form-input text-xs py-1 flex-1">
                                    <option value="">— none (use assigned) —</option>
                                    @foreach($allInstructors as $inst)
                                    <option value="{{ $inst->id }}"
                                        {{ $date->stand_in_instructor_id == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm text-xs py-1 px-3">Save</button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</x-app-layout>
