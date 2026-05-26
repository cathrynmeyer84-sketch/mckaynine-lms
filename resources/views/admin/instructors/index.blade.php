<x-app-layout :title="'Instructors'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Instructors</h1>
            <p class="page-subtitle">Manage training instructors</p>
        </div>
        <a href="{{ route('admin.instructors.create') }}" class="btn-primary">+ Add Instructor</a>
    </div>

    @if($instructors->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($instructors as $instructor)
        <div class="card flex flex-col">
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-brand flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-lg font-bold">{{ substr($instructor->first_name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900">{{ $instructor->first_name }} {{ $instructor->last_name }}</p>
                    @if($instructor->email)
                        <p class="text-sm text-gray-500 truncate">{{ $instructor->email }}</p>
                    @endif
                    @if($instructor->phone)
                        <p class="text-sm text-gray-500">{{ $instructor->phone }}</p>
                    @endif
                </div>
                @if($instructor->is_active)
                    <span class="badge badge-active text-xs flex-shrink-0">Active</span>
                @else
                    <span class="badge text-xs flex-shrink-0">Inactive</span>
                @endif
            </div>

            @if($instructor->bio)
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $instructor->bio }}</p>
            @endif

            <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">{{ $instructor->classes->count() }} class(es)</span>
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="btn-outline btn-sm">View Profile</a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <p>No instructors yet</p>
            <a href="{{ route('admin.instructors.create') }}" class="btn-primary btn-sm mt-3">Add first instructor</a>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
