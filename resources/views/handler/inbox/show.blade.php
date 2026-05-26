<x-app-layout :title="$conversation->subject">
<div class="page-content max-w-2xl">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('handler.inbox.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $conversation->subject }}</h1>
                @if($conversation->dogClass)
                <p class="page-subtitle">{{ $conversation->dogClass->name }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($conversation->messages as $message)
        <div class="card">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500">
                    {{ $message->isSystem() ? 'McKaynine' : ($message->sender?->name ?? 'Unknown') }}
                </p>
                <p class="text-xs text-gray-400">{{ $message->created_at->format('d M Y, g:ia') }}</p>
            </div>
            <x-message-blocks :blocks="$message->blocks" :class="$conversation->dogClass" />
        </div>
        @endforeach
    </div>

    @if(!$conversation->is_read_only)
    <div class="mt-4 card">
        <p class="text-xs font-semibold text-gray-500 mb-3">Reply</p>
        <form method="POST" action="{{ route('handler.inbox.reply', $conversation) }}">
            @csrf
            <textarea name="body" rows="4" class="input w-full" placeholder="Type your reply…" required></textarea>
            <div class="mt-3 flex justify-end">
                <button type="submit" class="btn-primary btn-sm">Send Reply</button>
            </div>
        </form>
    </div>
    @else
    <div class="mt-6 card bg-gray-50 text-center">
        <p class="text-sm text-gray-400">This message is read-only.</p>
        <a href="{{ route('handler.inbox.compose') }}" class="btn-outline btn-sm mt-3 inline-block">Send a new message to admin</a>
    </div>
    @endif

</div>
</x-app-layout>
