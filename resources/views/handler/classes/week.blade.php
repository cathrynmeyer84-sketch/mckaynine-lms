@extends('layouts.app')

@section('title', 'Week ' . ($classDate->week_number ?? '') . ' Content')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Week {{ $classDate->week_number ?? '' }}</h1>
        <p class="page-subtitle">{{ $enrolment->dogClass->name }} &middot; {{ $classDate->date->format('d M Y') }}</p>
    </div>
    <a href="{{ route('handler.classes.show', $enrolment) }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content">
    @php $content = $classDate->weeklyContent; @endphp

    @if($content)

    {{-- What to Bring --}}
    @if($content->what_to_bring)
    <div class="card mb-4">
        <h2 class="text-base font-semibold text-navy mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            What to Bring
        </h2>
        <div class="prose prose-sm text-gray-600 max-w-none">
            {!! nl2br(e($content->what_to_bring)) !!}
        </div>
    </div>
    @endif

    {{-- Checklist --}}
    @if($content->checklist && count($content->checklist))
    <div class="card mb-4" x-data="{ checked: {} }">
        <h2 class="text-base font-semibold text-navy mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Practice Checklist
        </h2>
        <div class="space-y-2">
            @foreach($content->checklist as $i => $item)
            <label class="flex items-start gap-3 cursor-pointer" x-data>
                <input type="checkbox" @change="$data.checked[{{ $i }}] = $event.target.checked" class="mt-0.5 w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand flex-shrink-0">
                <span :class="checked[{{ $i }}] ? 'line-through text-gray-400' : 'text-gray-700'" class="text-sm">{{ $item }}</span>
            </label>
            @endforeach
        </div>
    </div>
    @endif

    {{-- YouTube Embed --}}
    @if($content->youtube_url)
    <div class="card mb-4">
        <h2 class="text-base font-semibold text-navy mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            Training Video
        </h2>
        @php
            preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $content->youtube_url, $matches);
            $videoId = $matches[1] ?? null;
        @endphp
        @if($videoId)
        <div class="aspect-video rounded-xl overflow-hidden">
            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
        </div>
        @else
        <a href="{{ $content->youtube_url }}" target="_blank" class="btn btn-outline">Watch Video</a>
        @endif
    </div>
    @endif

    {{-- Additional Notes --}}
    @if($content->notes)
    <div class="card mb-4">
        <h2 class="text-base font-semibold text-navy mb-3">Notes</h2>
        <div class="prose prose-sm text-gray-600 max-w-none">
            {!! nl2br(e($content->notes)) !!}
        </div>
    </div>
    @endif

    @else
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-gray-500">No content available for this week yet.</p>
    </div>
    @endif
</div>
@endsection
