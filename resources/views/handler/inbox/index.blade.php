<x-app-layout title="Inbox">
<div class="page-content">

    <div class="page-header">
        <h1 class="page-title">Inbox</h1>
        <a href="{{ route('handler.inbox.compose') }}" class="btn-primary">+ New Message</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="space-y-2">
        @forelse($conversations as $conv)
        <a href="{{ route('handler.inbox.show', $conv) }}"
            class="card flex items-start gap-3 hover:bg-gray-50 transition-colors cursor-pointer {{ $conv->is_unread ? 'border-l-4 border-brand' : '' }}">
            <div class="shrink-0 mt-0.5">
                @if($conv->type === 'system' || $conv->type === 'class_announcement' || $conv->type === 'school_announcement')
                <div class="w-9 h-9 rounded-xl bg-brand/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                @else
                <div class="w-9 h-9 rounded-xl bg-navy/10 flex items-center justify-center text-xs font-bold text-navy">
                    MC
                </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-{{ $conv->is_unread ? 'bold' : 'medium' }} text-gray-900 truncate">
                        {{ $conv->subject }}
                        @if($conv->is_unread)
                        <span class="inline-block w-2 h-2 rounded-full bg-brand ml-1 align-middle"></span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 shrink-0">{{ ($conv->latestMessage?->created_at ?? $conv->created_at)->diffForHumans() }}</p>
                </div>
                @if($conv->dogClass)
                <p class="text-xs text-gray-400 mt-0.5">{{ $conv->dogClass->name }}</p>
                @endif
                @if($conv->is_read_only)
                <p class="text-xs text-gray-300 mt-0.5">Read only</p>
                @endif
            </div>
        </a>
        @empty
        <div class="card text-center py-12">
            <p class="text-gray-400">No messages yet.</p>
        </div>
        @endforelse
    </div>

</div>
</x-app-layout>
