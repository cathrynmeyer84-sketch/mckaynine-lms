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

        <div class="card" x-data="{
            open: {{ $loop->first ? 'true' : 'false' }},
            showTemplate: false,
            tab: 'edit',
            title: @js($content?->title ?? $template?->title ?? ''),
            description: @js($content?->description ?? $template?->description ?? ''),
            youtube_url: @js($content?->youtube_url ?? $template?->youtube_url ?? ''),
            practice_checklist: @js($content?->practice_checklist ?? $template?->practice_checklist ?? ''),
            what_to_bring: @js($content?->what_to_bring_next_week ?? $template?->what_to_bring_next_week ?? ''),
            get videoId() {
                const m = this.youtube_url.match(/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
                return m ? m[1] : null;
            },
            get checklistItems() {
                return this.practice_checklist.split('\n').map(l => l.replace(/^-\s*/, '').trim()).filter(l => l.length > 0);
            },
            nlbr(str) {
                return str ? str.replace(/\n/g, '<br>') : '';
            }
        }">

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
                            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                <button type="button" @click="tab = 'edit'"
                                    :class="tab === 'edit' ? 'bg-white shadow-sm text-navy' : 'text-gray-400 hover:text-gray-600'"
                                    class="px-3 py-1 rounded-md text-xs font-medium transition-all">
                                    Edit
                                </button>
                                <button type="button" @click="tab = 'preview'"
                                    :class="tab === 'preview' ? 'bg-white shadow-sm text-navy' : 'text-gray-400 hover:text-gray-600'"
                                    class="px-3 py-1 rounded-md text-xs font-medium transition-all flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Preview
                                </button>
                            </div>
                        </div>

                        {{-- Edit tab --}}
                        <div x-show="tab === 'edit'" x-cloak>
                        <div class="space-y-4">
                        <div>
                            <label class="form-label">Title</label>
                            <input type="text" name="title" x-model="title"
                                class="form-input w-full"
                                placeholder="{{ $template?->title ?: 'e.g. Week ' . $classDate->week_number . ' — Introduction' }}">
                        </div>

                        <div>
                            <label class="form-label">Description / Notes for Handlers</label>
                            <textarea name="description" rows="4" class="form-textarea w-full" x-model="description"
                                placeholder="What was covered this week..."></textarea>
                        </div>

                        <div>
                            <label class="form-label">YouTube Video URL</label>
                            <input type="url" name="youtube_url" x-model="youtube_url"
                                class="form-input w-full"
                                placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <div>
                            <label class="form-label">Practice Checklist</label>
                            <textarea name="practice_checklist" rows="4" class="form-textarea w-full" x-model="practice_checklist"
                                placeholder="- Practice sit for 5 minutes daily&#10;- Reward calm behaviour"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Use a dash ( - ) at the start of each line</p>
                        </div>

                        <div>
                            <label class="form-label">What to Bring Next Week</label>
                            <textarea name="what_to_bring_next_week" rows="2" class="form-textarea w-full" x-model="what_to_bring"
                                placeholder="High value treats, long line lead, etc."></textarea>
                        </div>

                        <div>
                            <label class="form-label">Extra Notes (admin only)</label>
                            <textarea name="extra_notes" rows="2" class="form-textarea w-full"
                                placeholder="Internal notes...">{{ old('extra_notes_' . $classDate->id, $content?->extra_notes ?? $template?->extra_notes) }}</textarea>
                        </div>
                        </div>
                        </div>

                        {{-- Preview tab --}}
                        <div x-show="tab === 'preview'" x-cloak>
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-4">Handler view preview</p>

                                {{-- Empty state --}}
                                <template x-if="!title && !description && !youtube_url && !practice_checklist && !what_to_bring">
                                    <p class="text-sm text-gray-400 italic text-center py-6">Fill in some fields to see the preview.</p>
                                </template>

                                {{-- Title --}}
                                <template x-if="title">
                                    <h2 class="text-base font-bold text-navy mb-4" x-text="title"></h2>
                                </template>

                                {{-- What to Bring --}}
                                <template x-if="what_to_bring">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3">
                                        <h3 class="text-sm font-semibold text-navy mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            What to Bring
                                        </h3>
                                        <p class="text-sm text-gray-600" x-html="nlbr(what_to_bring)"></p>
                                    </div>
                                </template>

                                {{-- Practice Checklist --}}
                                <template x-if="checklistItems.length > 0">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3">
                                        <h3 class="text-sm font-semibold text-navy mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                            Practice Checklist
                                        </h3>
                                        <div class="space-y-2">
                                            <template x-for="(item, i) in checklistItems" :key="i">
                                                <label class="flex items-start gap-3 cursor-pointer" x-data="{ checked: false }">
                                                    <input type="checkbox" x-model="checked" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-brand flex-shrink-0">
                                                    <span :class="checked ? 'line-through text-gray-400' : 'text-gray-700'" class="text-sm" x-text="item"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- YouTube embed --}}
                                <template x-if="videoId">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3">
                                        <h3 class="text-sm font-semibold text-navy mb-2 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                            Training Video
                                        </h3>
                                        <div class="aspect-video rounded-lg overflow-hidden bg-black">
                                            <iframe :src="'https://www.youtube.com/embed/' + videoId" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                        </div>
                                    </div>
                                </template>

                                {{-- YouTube URL present but invalid ID --}}
                                <template x-if="youtube_url && !videoId">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3">
                                        <h3 class="text-sm font-semibold text-navy mb-2">Training Video</h3>
                                        <p class="text-xs text-amber-600">⚠ Could not extract video ID from this URL. Check the URL format.</p>
                                    </div>
                                </template>

                                {{-- Description / Notes --}}
                                <template x-if="description">
                                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3">
                                        <h3 class="text-sm font-semibold text-navy mb-2">Notes</h3>
                                        <p class="text-sm text-gray-600" x-html="nlbr(description)"></p>
                                    </div>
                                </template>
                            </div>
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
