<x-app-layout :title="'Week ' . $classDate->week_number . ' Briefing'">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="page-title">Week {{ $classDate->week_number }} Briefing</h1>
            <p class="page-subtitle">{{ $class->name }} — {{ $classDate->date->format('D, d M Y') }}</p>
        </div>
    </div>
</div>

<div class="page-content space-y-4">
    @php $items = $classDate->classTypeWeek?->briefingItems ?? collect(); @endphp

    @if($items->isEmpty())
    <div class="empty-state">
        <p class="text-gray-500">No briefing exercises have been set up for this week.</p>
        <p class="text-xs text-gray-400 mt-1">Add them in Admin → Class Types → {{ $class->classType?->name }}.</p>
    </div>
    @else

    @foreach($items as $i => $item)
    <div class="card">
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center shrink-0">
                    <span class="text-xs font-bold text-brand">{{ $i + 1 }}</span>
                </div>
                <h2 class="font-semibold text-navy">{{ $item->exercise_name }}</h2>
            </div>
            @if($item->suggested_time)
            <span class="badge badge-pending text-xs shrink-0">⏱ {{ $item->suggested_time }}</span>
            @endif
        </div>

        @if($item->image_path)
        <img src="{{ Storage::url($item->image_path) }}"
            class="w-full rounded-xl object-cover mb-3" style="max-height:220px;">
        @endif

        @if($item->description)
        <p class="text-sm text-gray-700 leading-relaxed">{!! nl2br(e($item->description)) !!}</p>
        @endif
    </div>
    @endforeach

    @endif
</div>
</x-app-layout>
