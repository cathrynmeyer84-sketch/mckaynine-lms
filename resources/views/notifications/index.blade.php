@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="page-header">
    <h1 class="page-title">Notifications</h1>
    @if($notifications->total() > 0)
    <form action="{{ route('notifications.read-all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm">Mark all read</button>
    </form>
    @endif
</div>

<div class="page-content">
    @forelse($notifications as $notification)
    <div class="card mb-3 {{ !$notification->is_read ? 'border-l-4 border-l-brand' : '' }}">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="font-medium text-navy">{{ $notification->title }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $notification->message }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notification->is_read)
            <span class="w-2 h-2 rounded-full bg-brand flex-shrink-0 mt-1.5"></span>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <p class="text-gray-500">No notifications yet.</p>
    </div>
    @endforelse

    @if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
