<x-app-layout :title="'Email Templates'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Email Templates</h1>
            <p class="page-subtitle">Edit the content of automated emails sent to handlers</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @foreach($templates as $template)
        <div class="card flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-navy">{{ $template->name }}</p>
                <p class="text-sm text-gray-500 mt-0.5">{{ $template->subject }}</p>
                @if($template->available_placeholders)
                <p class="text-xs text-gray-400 mt-1">Placeholders: <span class="font-mono">{{ $template->available_placeholders }}</span></p>
                @endif
            </div>
            <a href="{{ route('admin.email-templates.edit', $template) }}" class="btn-outline btn-sm shrink-0">Edit</a>
        </div>
        @endforeach
    </div>

</div>
</x-app-layout>
