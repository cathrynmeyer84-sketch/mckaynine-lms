<x-app-layout title="Inbox">
<div class="page-content">

    <div class="page-header">
        <h1 class="page-title">Inbox</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inbox.templates.index') }}" class="btn-outline btn-sm">Templates</a>
            <a href="{{ route('admin.inbox.compose') }}" class="btn-primary">+ Compose</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="space-y-2">
        @forelse($conversations as $conv)
        @php $isUnread = $conv->isUnreadFor($userId); @endphp
        <a href="{{ route('admin.inbox.show', $conv) }}"
            class="card flex items-start gap-3 hover:bg-gray-50 transition-colors cursor-pointer {{ $isUnread ? 'border-l-4 border-brand' : '' }}">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-{{ $isUnread ? 'bold' : 'medium' }} text-gray-900 truncate flex-1">
                        {{ $conv->subject }}
                    </p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ match($conv->type) {
                        'direct' => 'bg-blue-50 text-blue-600',
                        'class_announcement' => 'bg-purple-50 text-purple-600',
                        'school_announcement' => 'bg-amber-50 text-amber-700',
                        default => 'bg-gray-100 text-gray-500',
                    } }}">{{ str_replace('_', ' ', $conv->type) }}</span>
                    <p class="text-xs text-gray-400 shrink-0">{{ ($conv->latestMessage?->created_at ?? $conv->created_at)->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-3 mt-1">
                    @if($conv->dogClass)
                    <p class="text-xs text-gray-400">{{ $conv->dogClass->name }}</p>
                    @endif
                    <p class="text-xs text-gray-400">
                        {{ $conv->participants->where('user_id', '!=', $userId)->map(fn($p) => $p->user?->name)->filter()->join(', ') }}
                    </p>
                </div>
            </div>
        </a>
        @empty
        <div class="card text-center py-12">
            <p class="text-gray-400">No messages yet.</p>
        </div>
        @endforelse
    </div>

    {{ $conversations->links() }}

</div>
</x-app-layout>
