<x-app-layout :title="'Classes'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Classes</h1>
            <p class="page-subtitle">All training classes</p>
        </div>
        <a href="{{ route('admin.classes.create') }}" class="btn-primary">+ Create Class</a>
    </div>

    {{-- Filters --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="sm:w-56">
                <select name="class_type_id" class="form-select w-full">
                    <option value="">All class types</option>
                    @foreach($classTypes as $type)
                        <option value="{{ $type->id }}" @selected(request('class_type_id') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-44">
                <select name="status" class="form-select w-full">
                    <option value="current" @selected(request('status', 'current') === 'current')>Current (active + upcoming)</option>
                    <option value="upcoming" @selected(request('status') === 'upcoming')>Upcoming only</option>
                    <option value="active" @selected(request('status') === 'active')>Active only</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                    <option value="all" @selected(request('status') === 'all')>All classes</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request('class_type_id') || request('status'))
                <a href="{{ route('admin.classes.index') }}" class="btn-outline">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card">
        @if($classes->count())
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Class Type</th>
                        <th>Instructor(s)</th>
                        <th>Enrolled</th>
                        <th>Dates</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                    <tr>
                        <td class="font-medium text-gray-900">{{ $class->name }}</td>
                        <td class="text-sm text-gray-600">{{ $class->classType?->name ?? '—' }}</td>
                        <td class="text-sm text-gray-600">
                            @foreach($class->instructors as $inst)
                                <span>{{ $inst->first_name }} {{ $inst->last_name }}@if($inst->pivot->is_lead) <span class="text-xs text-amber">(Lead)</span>@endif</span>
                                @if(!$loop->last)<br>@endif
                            @endforeach
                            @if($class->instructors->isEmpty())—@endif
                        </td>
                        <td>
                            <span class="text-sm font-medium text-gray-900">{{ $class->confirmedEnrolments->count() }}</span>
                            @if($class->max_capacity)
                                <span class="text-xs text-gray-400">/ {{ $class->max_capacity }}</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">
                            @if($class->start_date)
                                {{ $class->start_date->format('d M') }}
                                @if($class->end_date) – {{ $class->end_date->format('d M Y') }}@endif
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @php
                                $sc = match($class->status) {
                                    'active' => 'badge-active',
                                    'upcoming' => 'badge-pending',
                                    'completed' => 'badge-completed',
                                    default => 'badge'
                                };
                            @endphp
                            <span class="badge {{ $sc }}">{{ ucfirst($class->status ?? 'draft') }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.classes.show', $class) }}" class="btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $classes->withQueryString()->links() }}
        </div>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p>No classes found</p>
            <a href="{{ route('admin.classes.create') }}" class="btn-primary btn-sm mt-3">Create first class</a>
        </div>
        @endif
    </div>

</div>
</x-app-layout>
