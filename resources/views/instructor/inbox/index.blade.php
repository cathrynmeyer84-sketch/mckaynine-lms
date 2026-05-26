<x-app-layout title="Inbox">
<div class="page-content">

    <div class="page-header">
        <h1 class="page-title">Inbox</h1>
    </div>

    <div class="space-y-2">
        @forelse($conversations as $conv)
        <a href="{{ route('instructor.inbox.show', $conv) }}"
            class="card flex items-start gap-3 hover:bg-gray-50 transition-colors cursor-pointer {{ $conv->is_unread ? 'border-l-4 border-brand' : '' }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-sm font-{{ $conv->is_unread ? 'bold' : 'medium' }} text-gray-900 truncate">
                        {{ $conv->subject }}
                        @if($conv->is_unread)<span class="inline-block w-2 h-2 rounded-full bg-brand ml-1 align-middle"></span>@endif
                    </p>
                    <p class="text-xs text-gray-400 shrink-0">{{ ($conv->latestMessage?->created_at ?? $conv->created_at)->diffForHumans() }}</p>
                </div>
                @if($conv->dogClass)<p class="text-xs text-gray-400 mt-0.5">{{ $conv->dogClass->name }}</p>@endif
            </div>
        </a>
        @empty
        <div class="card text-center py-12"><p class="text-gray-400">No messages yet.</p></div>
        @endforelse
    </div>

</div>
</x-app-layout>
