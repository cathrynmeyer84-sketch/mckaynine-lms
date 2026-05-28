@extends('layouts.app')

@section('title', 'Year Calendar')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Year Calendar</h1>
        <p class="page-subtitle">Click any day to mark it as off — no class that day</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.calendar.index', ['year' => $year - 1]) }}" class="btn btn-outline btn-sm">← {{ $year - 1 }}</a>
        <span class="text-xl font-bold text-navy px-1">{{ $year }}</span>
        <a href="{{ route('admin.calendar.index', ['year' => $year + 1]) }}" class="btn btn-outline btn-sm">{{ $year + 1 }} →</a>
    </div>
</div>

<div class="page-content space-y-8">

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- 12-month grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($months as $month)
    <div class="card !p-4">
        <h3 class="text-sm font-bold text-navy mb-3 uppercase tracking-wider">{{ $month['name'] }}</h3>

        <table style="width:100%; border-collapse:separate; border-spacing:2px;">
            <thead>
                <tr>
                    @foreach(['S','M','T','W','T','F','S'] as $dow)
                    <th style="text-align:center; font-size:11px; color:#9ca3af; font-weight:500; padding-bottom:4px;">{{ $dow }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($month['weeks'] as $week)
                <tr>
                    @foreach($week as $dayData)
                    <td style="padding:1px;">
                        @if($dayData !== null)
                        <div x-data="{
                                date: '{{ $dayData['date'] }}',
                                active: {{ $dayData['active'] ? 'true' : 'false' }},
                                label: '{{ addslashes($dayData['label']) }}',
                                saving: false,
                                toggle() {
                                    if (this.active) {
                                        const reason = prompt('Reason for no class (e.g. Public Holiday, School Holiday):', this.label || '');
                                        if (reason === null) return;
                                        this.label = reason;
                                        this.active = false;
                                    } else {
                                        this.active = true;
                                        this.label = '';
                                    }
                                    this.save();
                                },
                                save() {
                                    this.saving = true;
                                    fetch('{{ route('admin.calendar.day.save') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({ date: this.date, is_active: this.active, label: this.label }),
                                    }).then(() => { this.saving = false; });
                                }
                            }">
                            <div @click="toggle()"
                                :style="active
                                    ? 'background:#fff; border:1px solid #001d6d; color:#001d6d;'
                                    : 'background:#fef3c7; border:1px solid #fcd34d; color:#92400e; text-decoration:line-through;'"
                                :title="!active && label ? label : ''"
                                style="aspect-ratio:1/1; border-radius:4px; font-size:11px; font-weight:500; cursor:pointer; position:relative; display:flex; align-items:center; justify-content:center; user-select:none; transition:background 0.15s;">
                                {{ $dayData['day'] }}
                                <span x-show="saving" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.8);border-radius:4px;" x-cloak>
                                    <svg style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" class="animate-spin">
                                        <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>

{{-- School Years --}}
<div class="card">
    <h2 class="text-base font-semibold text-navy mb-1">School Years</h2>
    <p class="text-sm text-gray-500 mb-5">
        Define school year windows. The handler calendar automatically shows whichever year
        contains today — or the next upcoming one if you've set it up in advance.
        You can add next year's dates now and the changeover happens automatically.
    </p>

    {{-- Existing years --}}
    @if($schoolYears->isNotEmpty())
    <div class="space-y-3 mb-6">
        @foreach($schoolYears as $sy)
        @php $isCurrent = $sy->start_date->lte(today()) && $sy->end_date->gte(today()); @endphp
        <div x-data="{ editing: false }" class="border rounded-lg px-4 py-3 {{ $isCurrent ? 'border-brand/40 bg-brand/5' : 'border-gray-200' }}">
            <div x-show="!editing" class="flex items-center gap-3">
                <div class="flex-1">
                    <span class="font-medium text-navy text-sm">{{ $sy->label }}</span>
                    @if($isCurrent)
                    <span class="ml-2 text-[10px] font-semibold bg-brand/20 text-brand rounded px-1.5 py-0.5 uppercase tracking-wide">Current</span>
                    @elseif($sy->start_date->gt(today()))
                    <span class="ml-2 text-[10px] font-semibold bg-amber/20 text-amber-700 rounded px-1.5 py-0.5 uppercase tracking-wide">Upcoming</span>
                    @else
                    <span class="ml-2 text-[10px] font-semibold bg-gray-100 text-gray-400 rounded px-1.5 py-0.5 uppercase tracking-wide">Past</span>
                    @endif
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $sy->start_date->format('d M Y') }} — {{ $sy->end_date->format('d M Y') }}
                    </p>
                </div>
                <button @click="editing = true" class="text-xs text-gray-400 hover:text-navy">Edit</button>
                <form method="POST" action="{{ route('admin.calendar.school-years.destroy', $sy) }}" onsubmit="return confirm('Remove this school year?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                </form>
            </div>
            <form x-show="editing" method="POST" action="{{ route('admin.calendar.school-years.update', $sy) }}" class="space-y-3">
                @csrf @method('PUT')
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">Label</label>
                        <input type="text" name="label" value="{{ $sy->label }}" class="input w-full" required>
                    </div>
                    <div>
                        <label class="label">Start date</label>
                        <input type="date" name="start_date" value="{{ $sy->start_date->format('Y-m-d') }}" class="input w-full" required>
                    </div>
                    <div>
                        <label class="label">End date</label>
                        <input type="date" name="end_date" value="{{ $sy->end_date->format('Y-m-d') }}" class="input w-full" required>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" @click="editing = false" class="btn btn-outline btn-sm">Cancel</button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Add new year --}}
    <div x-data="{ open: {{ $schoolYears->isEmpty() ? 'true' : 'false' }} }">
        <button @click="open = !open" class="btn btn-outline btn-sm flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add school year
        </button>
        <form x-show="open" x-cloak method="POST" action="{{ route('admin.calendar.school-years.store') }}" class="mt-4 space-y-3">
            @csrf
            <div class="grid grid-cols-3 gap-3 max-w-lg">
                <div>
                    <label class="label">Label</label>
                    <input type="text" name="label" class="input w-full" placeholder="e.g. 2027" required>
                </div>
                <div>
                    <label class="label">Start date</label>
                    <input type="date" name="start_date" class="input w-full" required>
                </div>
                <div>
                    <label class="label">End date</label>
                    <input type="date" name="end_date" class="input w-full" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Add year</button>
        </form>
    </div>
</div>

{{-- Off-Day Reminder Message Settings --}}
<div class="card">
    <h2 class="text-base font-semibold text-navy mb-1">Off-Day Reminder Message</h2>
    <p class="text-sm text-gray-500 mb-5">
        Automatically sends an in-app message to enrolled handlers before a cancelled class day.
        The message content is managed via the inbox message template.
        Available placeholders: <code class="text-xs bg-stone/30 px-1 rounded">@{{handler_name}}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">@{{dog_name}}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">@{{class_name}}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">@{{off_date}}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">@{{off_reason}}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">@{{next_class_date}}</code>
    </p>

    @if($reminderTemplate)
    <a href="{{ route('admin.inbox.templates.edit', $reminderTemplate) }}"
        class="inline-flex items-center gap-2 btn btn-secondary mb-6">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
        Edit reminder message template
    </a>
    @endif

    <form action="{{ route('admin.calendar.settings.save') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="label">Send reminder how many days before the cancelled class</label>
            <div class="flex items-center gap-3">
                <input type="number" name="off_day_reminder_days" min="1" max="14"
                    value="{{ $reminderDays }}"
                    class="input w-24">
                <span class="text-sm text-gray-500">days before the off day</span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>


</div>
@endsection
