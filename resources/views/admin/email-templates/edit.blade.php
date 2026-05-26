<x-app-layout :title="'Edit: ' . $emailTemplate->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.email-templates.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $emailTemplate->name }}</h1>
                <p class="page-subtitle">Edit email template</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if($emailTemplate->available_placeholders)
    <div class="card bg-brand/5 border border-brand/15 mb-6">
        <p class="text-sm font-medium text-navy mb-1">Available placeholders</p>
        <p class="text-sm text-gray-600 font-mono">{{ $emailTemplate->available_placeholders }}</p>
        <p class="text-xs text-gray-400 mt-1">Copy and paste these exactly into your subject or body.</p>
    </div>
    @endif

    <div class="card max-w-3xl">
        <form method="POST" action="{{ route('admin.email-templates.update', $emailTemplate) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="form-label">Subject Line</label>
                <input type="text" name="subject" value="{{ old('subject', $emailTemplate->subject) }}"
                    class="form-input w-full" required>
                @error('subject')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Email Body</label>
                <textarea name="body" rows="16" class="form-textarea w-full font-mono text-sm" required>{{ old('body', $emailTemplate->body) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Plain text. Line breaks are preserved. Placeholders in {curly_braces} are replaced automatically.</p>
                @error('body')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Save Template</button>
                <a href="{{ route('admin.email-templates.index') }}" class="btn-outline">Cancel</a>
            </div>
        </form>
    </div>

</div>
</x-app-layout>
