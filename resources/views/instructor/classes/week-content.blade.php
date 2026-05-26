<x-app-layout :title="'Week ' . $classDate->week_number . ' Content'">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="page-title">Week {{ $classDate->week_number }}</h1>
            <p class="page-subtitle">{{ $class->name }} — {{ $classDate->date->format('D, d M Y') }}</p>
        </div>
    </div>
    @if($classDate->weeklyContent)
        @if($classDate->weeklyContent->is_published)
            <span class="badge badge-active text-xs">Published</span>
        @else
            <span class="badge badge-pending text-xs">Draft</span>
        @endif
    @endif
</div>

<div class="page-content space-y-4">
@php
    $wc  = $classDate->weeklyContent;
    $ctw = $classDate->classTypeWeek;

    // Merge: WeeklyContent takes priority, ClassTypeWeek fills gaps
    $title       = $wc?->title                  ?: $ctw?->title;
    $description = $wc?->description            ?: $ctw?->description;
    $youtubeUrl  = $wc?->youtube_url            ?: $ctw?->youtube_url;
    $whatToBring = $wc?->what_to_bring_next_week ?: $ctw?->what_to_bring_next_week;
    $extraNotes  = $wc?->extra_notes            ?: $ctw?->extra_notes;

    // Checklist: WeeklyContent stores JSON, ClassTypeWeek stores newline text
    $checklist = null;
    if ($wc?->practice_checklist) {
        $checklist = is_array($wc->practice_checklist)
            ? $wc->practice_checklist
            : (json_decode($wc->practice_checklist, true)
                ?? array_values(array_filter(array_map(fn($l) => ltrim(trim($l), '- '), explode("\n", $wc->practice_checklist)))));
    } elseif ($ctw?->practice_checklist) {
        $checklist = array_values(array_filter(array_map(fn($l) => ltrim(trim($l), '- '), explode("\n", $ctw->practice_checklist))));
    }

    $hasContent = $title || $description || $youtubeUrl || $whatToBring || $checklist;
@endphp

@if(!$hasContent)
<div class="empty-state">
    <p class="text-gray-500">No content has been set up for this week yet.</p>
</div>
@else

    {{-- 1. Header --}}
    @if($title || $description)
    <div class="card">
        @if($title)<h2 class="font-semibold text-navy text-lg">{{ $title }}</h2>@endif
        @if($description)<p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $description }}</p>@endif
    </div>
    @endif

    {{-- 2. Video --}}
    @if($youtubeUrl)
    @php
        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $youtubeUrl, $matches);
        $videoId = $matches[1] ?? null;
    @endphp
    <div class="card">
        <h2 class="text-base font-semibold text-navy mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            Training Video
        </h2>
        @if($videoId)
        <div class="aspect-video rounded-xl overflow-hidden">
            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
        </div>
        @else
        <a href="{{ $youtubeUrl }}" target="_blank" class="btn btn-outline">Watch Video</a>
        @endif
    </div>
    @endif

    {{-- 3. Practice checklist --}}
    @if($checklist && count($checklist))
    <div class="card">
        <h2 class="text-base font-semibold text-navy mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Practice Checklist
        </h2>
        <div class="space-y-2">
            @foreach($checklist as $item)
            <div class="flex items-start gap-3 text-sm text-gray-700">
                <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                {{ $item }}
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 4. What to bring next week --}}
    @if($whatToBring)
    <div class="card">
        <h2 class="text-base font-semibold text-navy mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            What to Bring Next Week
        </h2>
        <p class="text-sm text-gray-700 leading-relaxed">{{ $whatToBring }}</p>
    </div>
    @endif

    {{-- Extra notes --}}
    @if($extraNotes)
    <div class="card">
        <h2 class="text-base font-semibold text-navy mb-2">Notes</h2>
        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $extraNotes }}</p>
    </div>
    @endif

@endif
</div>
</x-app-layout>
