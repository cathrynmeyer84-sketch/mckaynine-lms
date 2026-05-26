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

{{-- Off-Day Reminder Email Settings --}}
<div class="card">
    <h2 class="text-base font-semibold text-navy mb-1">Off-Day Reminder Email</h2>
    <p class="text-sm text-gray-500 mb-5">
        Automatically sent to enrolled handlers before a cancelled class day. Placeholders:
        <code class="text-xs bg-stone/30 px-1 rounded">{handler_name}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">{dog_name}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">{class_name}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">{off_date}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">{off_reason}</code>
        <code class="text-xs bg-stone/30 px-1 rounded">{next_class_date}</code>
    </p>

    <form action="{{ route('admin.calendar.settings.save') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label class="label">Subject line</label>
            <input type="text" name="settings[off_day_email_subject]"
                value="{{ $settings['off_day_email_subject'] ?? '' }}"
                placeholder="e.g. No class on {off_date} – {off_reason}"
                class="input w-full max-w-xl">
        </div>
        <div>
            <label class="label">Email body</label>
            <textarea name="settings[off_day_email_body]" rows="7"
                placeholder="Hi {handler_name}, just a quick reminder that there is no class for {dog_name} ({class_name}) on {off_date} due to {off_reason}. Your next class is on {next_class_date}. See you then!"
                class="input w-full max-w-xl font-mono text-sm">{{ $settings['off_day_email_body'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="label">Send reminder how many days before the cancelled class</label>
            <div class="flex items-center gap-3">
                <input type="number" name="settings[off_day_reminder_days]" min="1" max="14"
                    value="{{ $settings['off_day_reminder_days'] ?? 3 }}"
                    class="input w-24">
                <span class="text-sm text-gray-500">days before the off day</span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Email Settings</button>
    </form>
</div>


</div>
@endsection
