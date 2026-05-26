<x-app-layout title="Messages">
<div class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Messages</h1>
            <p class="page-subtitle">Sent to handlers</p>
        </div>
        <a href="{{ route('admin.inbox.compose') }}" class="btn-primary">Compose Message</a>
    </div>

    @if($messages->count())
    <div class="card">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Handler</th>
                        <th>Subject</th>
                        <th>Sent by</th>
                        <th>Sent</th>
                        <th>Read</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                    <tr>
                        <td class="font-medium text-gray-900">{{ $message->handler->full_name }}</td>
                        <td class="text-sm text-gray-700">{{ $message->subject }}</td>
                        <td class="text-sm text-gray-500">{{ $message->sentBy->name }}</td>
                        <td class="text-sm text-gray-500">{{ $message->created_at->format('d M Y') }}</td>
                        <td>
                            @if($message->read_at)
                            <span class="text-xs text-green-600 font-medium">Read {{ $message->read_at->format('d M') }}</span>
                            @else
                            <span class="text-xs text-gray-400">Unread</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $messages->links() }}</div>
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <p>No messages sent yet.</p>
            <a href="{{ route('admin.inbox.compose') }}" class="btn-primary btn-sm mt-3">Compose First Message</a>
        </div>
    </div>
    @endif
</div>
</x-app-layout>
