<x-app-layout title="New Message">
<div class="page-content max-w-xl">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('handler.inbox.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="page-title">New Message</h1>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('handler.inbox.store') }}" class="space-y-4">
            @csrf
            <p class="text-xs text-gray-400">Sending to: <span class="font-medium text-gray-700">McKaynine Admin</span></p>

            <div>
                <label class="form-label">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="form-input w-full" required>
                @error('subject')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Message</label>
                <textarea name="body" rows="6" class="form-textarea w-full" required>{{ old('body') }}</textarea>
                @error('body')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-primary w-full">Send Message</button>
        </form>
    </div>

</div>
</x-app-layout>
