<x-app-layout :title="'Handlers'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Handlers</h1>
            <p class="page-subtitle">Manage all registered handlers</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('admin.handlers.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name or phone..."
                    class="form-input w-full">
            </div>
            <div class="sm:w-44">
                <select name="status" class="form-select w-full">
                    <option value="">All statuses</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.handlers.index') }}" class="btn-outline">Clear</a>
            @endif
        </form>
    </div>

    {{-- Desktop Table --}}
    <div class="card hidden md:block">
        @if($handlers->count())
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Handler</th>
                        <th>Contact</th>
                        <th>Dogs</th>
                        <th>Enrolments</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($handlers as $handler)
                    <tr>
                        <td>
                            <div class="font-medium text-gray-900">{{ $handler->first_name }} {{ $handler->last_name }}</div>
                            @if($handler->user)
                                <div class="text-xs text-gray-500">{{ $handler->user->email }}</div>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">{{ $handler->cell_number ?? '—' }}</td>
                        <td>
                            <span class="text-sm text-gray-700">{{ $handler->dogs->count() }}
                                @if($handler->dogs->count())
                                    <span class="text-gray-400 text-xs">({{ $handler->dogs->pluck('name')->join(', ') }})</span>
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="text-sm text-gray-700">{{ $handler->enrolments->count() }}</span>
                            @foreach($handler->enrolments->take(2) as $enrolment)
                                <div class="text-xs text-gray-400">{{ $enrolment->dogClass?->name }}</div>
                            @endforeach
                        </td>
                        <td>
                            @php
                                $statusClass = match($handler->status) {
                                    'active' => 'badge-active',
                                    'pending' => 'badge-pending',
                                    default => 'badge'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ ucfirst($handler->status ?? 'pending') }}</span>
                        </td>
                        <td class="text-sm text-gray-500">{{ $handler->created_at?->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.handlers.show', $handler) }}" class="btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $handlers->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p>No handlers found</p>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.handlers.index') }}" class="btn-outline btn-sm mt-3">Clear filters</a>
            @endif
        </div>
        @endif
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse($handlers as $handler)
        <div class="card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-semibold text-gray-900">{{ $handler->first_name }} {{ $handler->last_name }}</p>
                    @if($handler->user)
                        <p class="text-sm text-gray-500">{{ $handler->user->email }}</p>
                    @endif
                    @if($handler->cell_number)
                        <p class="text-sm text-gray-500">{{ $handler->cell_number }}</p>
                    @endif
                </div>
                @php
                    $statusClass = match($handler->status) {
                        'active' => 'badge-active',
                        'pending' => 'badge-pending',
                        default => 'badge'
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($handler->status ?? 'pending') }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                <span>{{ $handler->dogs->count() }} dog(s) &middot; {{ $handler->enrolments->count() }} enrolment(s)</span>
                <a href="{{ route('admin.handlers.show', $handler) }}" class="btn-primary btn-sm">View</a>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p>No handlers found</p>
            </div>
        </div>
        @endforelse
        @if($handlers->hasPages())
        <div class="py-2">
            {{ $handlers->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
</x-app-layout>
