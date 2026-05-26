<x-app-layout :title="'Content: ' . $class->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Weekly Content</h1>
                <p class="page-subtitle">{{ $class->name }}</p>
            </div>
        </div>
        @if($class->classType?->has_structured_content)
        <a href="{{ route('admin.classes.content-schedule', $class) }}" class="btn-outline btn-sm">
            Edit content schedule
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if($class->dates->count())
    <div class="space-y-4">
        @foreach($class->dates as $classDate)
        @php
            $content  = $classDate->weeklyContent;
            $template = $classDate->classTypeWeek;
        @endphp

        @if($classDate->is_off_week)
        {{-- Off-week / calendar break --}}
        <div class="flex items-center gap-3 px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span><strong>No class</strong> — {{ $classDate->date->format('l, d M Y') }} &mdash; {{ $classDate->off_week_reason ?? 'Scheduled break' }}</span>
        </div>
        @continue
        @endif

        <div class="card" x-data="{ open: {{ $loop->first ? 'true' : 'false' }}, showTemplate: false }">

            {{-- Card Header --}}
            <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between text-left">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ $content ? 'bg-brand' : 'bg-gray-100' }} flex items-center justify-center shrink-0">
                        @if($content)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <span class="text-gray-400 text-sm font-bold">{{ $classDate->week_number }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">
                            Week {{ $classDate->week_number }} &mdash;
                            {{ $classDate->date->format('l, d M Y') }}
                        </p>
                        <p class="text-xs text-gray-500 flex items-center gap-2">
                            @if($classDate->start_time){{ \Carbon\Carbon::parse($classDate->start_time)->format('g:i A') }}@endif
                            @if($template)
                            <span class="badge badge-sm badge-upcoming">{{ $template->title ?: 'Week ' . $template->week_number . ' template' }}</span>
                            @else
                            <span class="text-gray-400">No template mapped</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($classDate->content_send_date)
                        <span class="text-xs text-gray-400 hidden sm:block">
                            Sends {{ $classDate->content_send_date->format('d M, H:i') }}
                        </span>
                    @endif
                    @if($content)
                        @if($content->is_published)
                            <span class="badge badge-active text-xs">Published</span>
                        @elseif($content->publish_at)
                            <span class="badge badge-pending text-xs">Scheduled</span>
                        @else
                            <span class="badge text-xs">Draft</span>
                        @endif
                    @else
                        <span class="badge text-xs">No content</span>
                    @endif
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            {{-- Expanded body --}}
            <div x-show="open" x-collapse class="mt-6 border-t border-gray-100 pt-6 space-y-6">

                {{-- Template preview --}}
                @if($template)
                <div class="bg-brand/5 border border-brand/15 rounded-xl overflow-hidden">
                    <button type="button" @click="showTemplate = !showTemplate"
                        class="w-full flex items-center justify-between px-4 py-3 text-left">
                        <span class="text-sm font-semibold text-navy">
                            Template: Week {{ $template->week_number }}@if($template->title) — {{ $template->title }}@endif
                        </span>
                        <svg class="w-4 h-4 text-brand transition-transform" :class="showTemplate ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="showTemplate" x-collapse class="px-4 pb-4 space-y-3 text-sm border-t border-brand/15">
                        @if($template->description)
                        <div class="pt-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Description</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $template->description }}</p>
                        </div>
                        @endif
                        @if($template->youtube_url)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Video</p>
                            <a href="{{ $template->youtube_url }}" target="_blank" class="text-brand underline break-all">{{ $template->youtube_url }}</a>
                        </div>
                        @endif
                        @if($template->practice_checklist)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Practice Checklist</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $template->practice_checklist }}</p>
                        </div>
                        @endif
                        @if($template->what_to_bring_next_week)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">What to Bring Next Week</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $template->what_to_bring_next_week }}</p>
                        </div>
                        @endif
                        @if(!$template->description && !$template->youtube_url && !$template->practice_checklist && !$template->what_to_bring_next_week)
                        <p class="text-gray-400 italic pt-3">Template has no content yet. <a href="{{ route('admin.class-types.edit', $class->classType) }}" class="text-brand underline">Edit template →</a></p>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Session content form --}}
                <form method="POST" action="{{ route('admin.classes.content.store', [$class, $classDate]) }}">
                    @csrf
                    <div class="space-y-4">

                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700">Session Content</h3>
                            <p class="text-xs text-gray-400">Overrides or adds to the template above</p>
                        </div>

                        <div>
                            <label class="form-label">Title</label>
                            <input type="text" name="title"
                                value="{{ old('title_' . $classDate->id, $content?->title ?? $template?->title) }}"
                                class="form-input w-full"
                                placeholder="{{ $template?->title ?: 'e.g. Week ' . $classDate->week_number . ' — Introduction' }}">
                        </div>

                        <div>
                            <label class="form-label">Description / Notes for Handlers</label>
                            <textarea name="description" rows="4" class="form-textarea w-full"
                                placeholder="What was covered this week...">{{ old('description_' . $classDate->id, $content?->description ?? $template?->description) }}</textarea>
                        </div>

                        <div>
                            <label class="form-label">YouTube Video URL</label>
                            <input type="url" name="youtube_url"
                                value="{{ old('youtube_url_' . $classDate->id, $content?->youtube_url ?? $template?->youtube_url) }}"
                                class="form-input w-full"
                                placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <div>
                            <label class="form-label">Practice Checklist</label>
                            <textarea name="practice_checklist" rows="4" class="form-textarea w-full"
                                placeholder="- Practice sit for 5 minutes daily&#10;- Reward calm behaviour">{{ old('practice_checklist_' . $classDate->id, $content?->practice_checklist ?? $template?->practice_checklist) }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Use a dash ( - ) at the start of each line</p>
                        </div>

                        <div>
                            <label class="form-label">What to Bring Next Week</label>
                            <textarea name="what_to_bring_next_week" rows="2" class="form-textarea w-full"
                                placeholder="High value treats, long line lead, etc.">{{ old('what_to_bring_next_week_' . $classDate->id, $content?->what_to_bring_next_week ?? $template?->what_to_bring_next_week) }}</textarea>
                        </div>

                        <div>
                            <label class="form-label">Extra Notes (admin only)</label>
                            <textarea name="extra_notes" rows="2" class="form-textarea w-full"
                                placeholder="Internal notes...">{{ old('extra_notes_' . $classDate->id, $content?->extra_notes ?? $template?->extra_notes) }}</textarea>
                        </div>

                        {{-- Send & publish settings --}}
                        <div class="bg-gray-50 rounded-xl p-4 space-y-3"
                            x-data="{ scheduled: {{ $content?->publish_at && !$content?->is_published ? 'true' : 'false' }} }">

                            <p class="text-sm font-semibold text-gray-700">Send Settings</p>

                            <div>
                                <label class="form-label">Content Email Send Time</label>
                                <input type="datetime-local" name="content_send_date"
                                    value="{{ old('content_send_date_' . $classDate->id, $classDate->content_send_date?->format('Y-m-d\TH:i')) }}"
                                    class="form-input w-full">
                                <p class="text-xs text-gray-400 mt-1">Auto-set to 1 hour after class end. Edit here to override.</p>
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_published_{{ $classDate->id }}" name="is_published" value="1"
                                    class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                    @checked(old('is_published', $content?->is_published))>
                                <label for="is_published_{{ $classDate->id }}" class="text-sm font-medium text-gray-700">Publish now (visible to handlers immediately)</label>
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="scheduled_{{ $classDate->id }}"
                                    class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                    x-model="scheduled">
                                <label for="scheduled_{{ $classDate->id }}" class="text-sm font-medium text-gray-700">Schedule a publish date/time</label>
                            </div>
                            <div x-show="scheduled" class="pl-7">
                                <input type="datetime-local" name="publish_at"
                                    value="{{ old('publish_at', $content?->publish_at?->format('Y-m-d\TH:i')) }}"
                                    class="form-input w-full">
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-primary">Save Content</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p>No class sessions scheduled</p>
            <a href="{{ route('admin.classes.edit', $class) }}" class="btn-outline btn-sm mt-3">Edit class settings</a>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
