<x-app-layout title="Compose Message">
<div class="page-content">
    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.messages.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="page-title">Compose Message</h1>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.messages.store') }}" class="card space-y-5">
            @csrf

            <div>
                <label class="form-label">To <span class="text-red-500">*</span></label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-xl p-3">
                    @foreach($handlers as $handler)
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 rounded-lg p-1">
                        <input type="checkbox" name="handler_ids[]" value="{{ $handler->id }}"
                            class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                            {{ $preselectedHandler?->id === $handler->id ? 'checked' : '' }}>
                        <span class="text-sm text-gray-800">{{ $handler->full_name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('handler_ids')<p class="form-error">Please select at least one handler.</p>@enderror
            </div>

            <div>
                <label class="form-label">Subject <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="form-input w-full" required
                    placeholder="e.g. Your Next Steps at McKaynine">
                @error('subject')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Message <span class="text-red-500">*</span></label>
                <textarea name="body" rows="10" class="form-textarea w-full" required
                    placeholder="Write your message here...">{{ old('body') }}</textarea>
                @error('body')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1">Send Message</button>
                <a href="{{ route('admin.messages.index') }}" class="btn-outline flex-1 text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
