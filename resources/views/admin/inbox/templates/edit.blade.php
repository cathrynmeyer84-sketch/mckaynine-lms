<x-app-layout :title="'Edit Template — ' . $template->name">
<div class="page-content max-w-2xl">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inbox.templates.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $template->name }}</h1>
                <p class="page-subtitle">Edit template content</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-success mb-4">{{ session('success') }}</div>
    @endif

    {{-- Merge tag reference --}}
    <div class="card mb-6 bg-brand/5 border-brand/20">
        <p class="text-xs font-semibold text-brand uppercase tracking-wide mb-2">Available Merge Tags</p>
        <div class="flex flex-wrap gap-2">
            @foreach(['{{handler_name}}','{{dog_name}}','{{class_name}}','{{class_location}}','{{class_start_date}}','{{class_start_time}}','{{class_end_time}}','{{instructor_names}}','{{week_number}}'] as $tag)
            <code class="text-xs bg-white border border-brand/20 text-brand px-2 py-0.5 rounded-lg font-mono">{{ $tag }}</code>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-2">These will be replaced with real data when the message is sent.</p>
    </div>

    <form method="POST" action="{{ route('admin.inbox.templates.update', $template) }}">
        @csrf

        {{-- Subject --}}
        <div class="card mb-4">
            <label class="label">Subject Line</label>
            <input type="text" name="subject" class="input" value="{{ old('subject', $template->subject) }}" required>
        </div>

        {{-- Blocks --}}
        <div class="space-y-3 mb-6">
            @foreach($template->blocks as $i => $block)

            @if($block['type'] === 'text')
            <div class="card">
                <label class="label">Text Block</label>
                <textarea name="blocks[{{ $i }}][content]" rows="5" class="input font-mono text-sm">{{ old("blocks.$i.content", $block['content'] ?? '') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Supports **bold** and merge tags. Line breaks are preserved.</p>
            </div>

            @elseif($block['type'] === 'button')
            <div class="card">
                <label class="label">Button</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500">Label</label>
                        <input type="text" name="blocks[{{ $i }}][label]" class="input mt-1"
                            value="{{ old("blocks.$i.label", $block['label'] ?? '') }}">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">URL (or path)</label>
                        <input type="text" name="blocks[{{ $i }}][url]" class="input mt-1"
                            value="{{ old("blocks.$i.url", $block['url'] ?? '') }}" placeholder="/my/achievements">
                    </div>
                </div>
            </div>

            @elseif($block['type'] === 'divider')
            <div class="flex items-center gap-3 px-1">
                <hr class="flex-1 border-gray-200">
                <span class="text-xs text-gray-300 uppercase tracking-wide">Divider</span>
                <hr class="flex-1 border-gray-200">
            </div>

            @elseif($block['type'] === 'class_info')
            <div class="card border-dashed border-brand/30 bg-brand/5 flex items-center gap-3">
                <svg class="w-5 h-5 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-navy">Class Info Block</p>
                    <p class="text-xs text-gray-400">Auto-filled with class name, location, dates and instructor — not editable.</p>
                </div>
            </div>

            @elseif($block['type'] === 'class_content')
            <div class="card border-dashed border-amber/30 bg-amber/5 flex items-center gap-3">
                <svg class="w-5 h-5 text-amber shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <div>
                    <p class="text-sm font-semibold text-navy">Weekly Content Block</p>
                    <p class="text-xs text-gray-400">Auto-filled with lesson title, description, video, practice checklist and what to bring — not editable.</p>
                </div>
            </div>

            @elseif($block['type'] === 'next_class')
            <div class="card border-dashed border-gray-200 bg-gray-50 flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                <div>
                    <p class="text-sm font-semibold text-navy">Next Class Block</p>
                    <p class="text-xs text-gray-400">Auto-filled with links to recommended next classes — not editable.</p>
                </div>
            </div>

            @else
            <div class="card border-dashed border-gray-200 bg-gray-50">
                <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $block['type'] }} block — not editable here</p>
            </div>
            @endif

            @endforeach
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Save Template</button>
        </div>

    </form>

</div>
</x-app-layout>
