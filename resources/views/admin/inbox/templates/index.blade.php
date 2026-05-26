<x-app-layout title="Message Templates">
<div class="page-content max-w-2xl">

    <div class="page-header">
        <div>
            <h1 class="page-title">Message Templates</h1>
            <p class="page-subtitle">Automated messages sent to handlers</p>
        </div>
        <a href="{{ route('admin.inbox.index') }}" class="btn-outline btn-sm">← Inbox</a>
    </div>

    <div class="space-y-3">
        @foreach($templates as $template)
        <div class="card flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-navy text-sm">{{ $template->name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $template->subject }}</p>
                <p class="text-xs text-gray-300 mt-0.5">Slug: <code class="font-mono">{{ $template->slug }}</code></p>
            </div>
            <a href="{{ route('admin.inbox.templates.edit', $template) }}" class="btn-outline btn-sm shrink-0">Edit</a>
        </div>
        @endforeach
    </div>

</div>
</x-app-layout>
