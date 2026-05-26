@extends('layouts.app')

@section('title', 'Content Schedule — ' . $class->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Content Schedule</h1>
        <p class="page-subtitle">{{ $class->name }} — set when each week's content goes out</p>
    </div>
    <a href="{{ route('admin.classes.show', $class) }}" class="btn btn-outline">← Back to class</a>
</div>

<div class="page-content">

@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif

@if(!$class->classType || !$class->classType->has_structured_content)
<div class="card text-center py-10">
    <p class="text-gray-500 mb-4">This class is not linked to a class type with structured content.</p>
    <a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-outline">Edit class settings</a>
</div>
@else

<form action="{{ route('admin.classes.content-schedule.save', $class) }}" method="POST">
@csrf

<div class="card mb-6">
    <div class="flex items-center gap-3 mb-1">
        <p class="font-semibold text-navy">{{ $class->classType->name }}</p>
        <span class="badge badge-upcoming">{{ $class->classType->duration_label }}</span>
    </div>
    <p class="text-sm text-gray-500">
        Map each class session to a week of content and set the date the content email should go out.
        Leave blank to skip content for that session.
    </p>
</div>

<div class="space-y-3 mb-6">
    @php $weeks = $class->classType->weeks->keyBy('week_number'); @endphp

    @forelse($class->dates as $classDate)
    <div class="card !p-4 flex items-center gap-4 flex-wrap">
        {{-- Session info --}}
        <div class="w-36 shrink-0">
            <p class="text-sm font-semibold text-navy">{{ $classDate->date->format('D d M Y') }}</p>
            @if($classDate->week_number)
            <p class="text-xs text-gray-400">Session {{ $classDate->week_number }}</p>
            @endif
        </div>

        {{-- Week selector --}}
        <div class="flex-1 min-w-40">
            <label class="text-xs text-gray-400 block mb-1">Content week</label>
            <select name="schedule[{{ $classDate->id }}][class_type_week_id]" class="input input-sm w-full">
                <option value="">— none —</option>
                @foreach($weeks as $week)
                <option value="{{ $week->id }}"
                    {{ $classDate->class_type_week_id == $week->id ? 'selected' : '' }}>
                    Week {{ $week->week_number }}{{ $week->title ? ' — ' . $week->title : '' }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Auto send time --}}
        <div class="shrink-0 text-right">
            @if($classDate->content_sent_at)
            <span class="badge badge-confirmed text-xs">Sent {{ $classDate->content_sent_at->format('d M H:i') }}</span>
            @elseif($classDate->content_send_date)
            <span class="text-xs text-gray-400">Sends {{ $classDate->content_send_date->format('H:i') }}</span>
            @else
            <span class="text-xs text-gray-300">No send time</span>
            @endif
        </div>
    </div>
    @empty
    <div class="card text-center py-6">
        <p class="text-gray-400 text-sm">No class sessions found. Generate class dates first.</p>
    </div>
    @endforelse
</div>

<button type="submit" class="btn btn-primary">Save Content Schedule</button>
</form>

@endif
</div>
@endsection
