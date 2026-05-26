<x-app-layout :title="'Enrolments'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Enrolments</h1>
            <p class="page-subtitle">All active enrolments requiring attention</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    {{-- Class Confirmations (existing handlers re-enrolling) --}}
    @if($classConfirmations->count())
    <div class="mb-8">
        <h2 class="text-base font-semibold text-gray-700 mb-3">Class Confirmations</h2>
        <div class="card">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Handler</th>
                            <th>Dog</th>
                            <th>Class</th>
                            <th>Enrolled</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classConfirmations as $enrolment)
                        <tr>
                            <td class="font-medium text-gray-900">{{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }}</td>
                            <td class="text-sm text-gray-600">{{ $enrolment->dog?->name ?? '—' }}</td>
                            <td class="text-sm text-gray-600">{{ $enrolment->dogClass?->name ?? '—' }}</td>
                            <td class="text-sm text-gray-500">{{ $enrolment->enrolled_at?->format('d M Y') ?? '—' }}</td>
                            <td><a href="{{ route('admin.enrolments.show', $enrolment) }}" class="btn-outline btn-sm">Confirm</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- New Enrolments --}}
    <div>
        @if($classConfirmations->count())
        <h2 class="text-base font-semibold text-gray-700 mb-3">New Enrolments</h2>
        @endif
        <div class="card">
            @if($enrolments->count())
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Handler</th>
                            <th>Dog</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Enrolled</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrolments as $enrolment)
                        <tr>
                            <td class="font-medium text-gray-900">{{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }}</td>
                            <td class="text-sm text-gray-600">{{ $enrolment->dog?->name ?? '—' }}</td>
                            <td class="text-sm text-gray-600">{{ $enrolment->dogClass?->name ?? '—' }}</td>
                            <td><span class="badge {{ $enrolment->status_badge_class }}">{{ $enrolment->status_label }}</span></td>
                            <td class="text-sm text-gray-500">{{ $enrolment->enrolled_at?->format('d M Y') ?? '—' }}</td>
                            <td><a href="{{ route('admin.enrolments.show', $enrolment) }}" class="btn-outline btn-sm">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">{{ $enrolments->links() }}</div>
            @else
            <div class="empty-state py-12">
                <p>No active enrolments</p>
            </div>
            @endif
        </div>
    </div>

</div>
</x-app-layout>
