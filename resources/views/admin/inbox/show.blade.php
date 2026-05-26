<x-app-layout :title="$conversation->subject">
<div class="page-content max-w-2xl">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inbox.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $conversation->subject }}</h1>
                <p class="page-subtitle">
                    {{ $conversation->participants->map(fn($p) => $p->user?->name)->filter()->join(' · ') }}
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @foreach($conversation->messages as $message)
        <div class="card {{ $message->sender_user_id === auth()->id() ? 'bg-brand/5 border-brand/20' : '' }}">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500">
                    {{ $message->isSystem() ? 'McKaynine (System)' : ($message->sender?->name ?? 'Unknown') }}
                </p>
                <p class="text-xs text-gray-400">{{ $message->created_at->format('d M Y, g:ia') }}</p>
            </div>
            <x-message-blocks :blocks="$message->blocks" :class="$conversation->dogClass" />
        </div>
        @endforeach
    </div>

    @if(!$conversation->is_read_only)
    <div class="mt-6 card">
        <p class="text-sm font-semibold text-gray-700 mb-3">Reply</p>
        <form method="POST" action="{{ route('admin.inbox.reply', $conversation) }}" class="space-y-3">
            @csrf
            <textarea name="body" rows="4" class="form-textarea w-full" placeholder="Write your reply…" required></textarea>
            <button type="submit" class="btn-primary">Send Reply</button>
        </form>
    </div>
    @endif

</div>
</x-app-layout>
