<x-app-layout title="Private Lessons">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Private Lessons</h1>
            <p class="page-subtitle">{{ $lessons->total() }} lesson{{ $lessons->total() !== 1 ? 's' : '' }} total</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    {{-- Filter tabs --}}
    @php $filter = request('status', ''); @endphp
    <div class="flex gap-2 mb-6 flex-wrap">
        @foreach([''=>'All', 'pending'=>'Pending', 'confirmed'=>'Confirmed', 'completed'=>'Completed', 'cancelled'=>'Cancelled'] as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium transition-colors
               {{ $filter === $val ? 'bg-navy text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="card">
        @if($lessons->count())
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Handler</th>
                        <th>Dog</th>
                        <th>Instructor</th>
                        <th>Date &amp; Time</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lessons as $lesson)
                    <tr>
                        <td class="font-medium text-gray-900">{{ $lesson->handler?->full_name ?? '—' }}</td>
                        <td class="text-sm text-gray-600">{{ $lesson->dog?->name ?? '—' }}</td>
                        <td class="text-sm text-gray-600">{{ $lesson->instructor?->full_name ?? '—' }}</td>
                        <td class="text-sm text-gray-600">
                            {{ $lesson->requested_date?->format('d M Y') ?? '—' }}
                            @if($lesson->requested_start_time)
                            <span class="text-gray-400">at</span>
                            {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">
                            @if($lesson->fee !== null)
                            R {{ number_format($lesson->fee, 2) }}
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $lesson->status_badge_class }}">{{ $lesson->status_label }}</span></td>
                        <td><a href="{{ route('admin.private-lessons.show', $lesson) }}" class="btn-outline btn-sm">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $lessons->links() }}</div>
        @else
        <div class="empty-state py-16">
            <div class="empty-state-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p>No private lessons found.</p>
        </div>
        @endif
    </div>

</div>
</x-app-layout>
