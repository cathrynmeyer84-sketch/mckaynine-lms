<x-app-layout :title="$instructor->first_name . ' ' . $instructor->last_name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.instructors.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $instructor->first_name }} {{ $instructor->last_name }}</h1>
                <p class="page-subtitle">Instructor profile</p>
            </div>
        </div>
        <a href="{{ route('admin.instructors.edit', $instructor) }}" class="btn-primary">Edit Profile</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info --}}
        <div class="space-y-6">
            <div class="card">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-brand flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($instructor->profile_photo_path)
                        <img src="{{ Storage::url($instructor->profile_photo_path) }}" alt="{{ $instructor->first_name }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-white text-2xl font-bold">{{ substr($instructor->first_name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-lg">{{ $instructor->first_name }} {{ $instructor->last_name }}</p>
                        @if($instructor->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-3">
                    @if($instructor->email)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                        <p class="text-sm text-gray-700">{{ $instructor->email }}</p>
                    </div>
                    @endif
                    @if($instructor->phone)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Contact Number</p>
                        <p class="text-sm text-gray-700">{{ $instructor->phone }}</p>
                    </div>
                    @endif
                    @if($instructor->birthday)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Birthday</p>
                        <p class="text-sm text-gray-700">{{ $instructor->birthday->format('d M Y') }}</p>
                    </div>
                    @endif
                    @if($instructor->user)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Login Account</p>
                        <p class="text-sm text-gray-700">{{ $instructor->user->email }}</p>
                    </div>
                    @endif
                    @if($instructor->bio)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Bio</p>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $instructor->bio }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Classes --}}
        <div class="lg:col-span-2">
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">
                    Classes
                    <span class="text-gray-400 font-normal text-sm ml-1">({{ $instructor->classes->count() }})</span>
                </h2>

                @if($instructor->classes->count())
                <div class="space-y-3">
                    @foreach($instructor->classes->sortByDesc('start_date') as $class)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium text-gray-900">{{ $class->name }}</p>
                            <p class="text-sm text-gray-500">{{ $class->classType?->name ?? '' }}</p>
                            @if($class->start_date)
                                <p class="text-xs text-gray-400">{{ $class->start_date->format('d M Y') }}
                                    @if($class->end_date) – {{ $class->end_date->format('d M Y') }}@endif
                                </p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $class->dates->count() }} sessions</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $sc = match($class->status) {
                                    'active' => 'badge-active',
                                    'upcoming' => 'badge-pending',
                                    'completed' => 'badge-completed',
                                    default => 'badge'
                                };
                            @endphp
                            <span class="badge {{ $sc }}">{{ ucfirst($class->status ?? 'draft') }}</span>
                            <a href="{{ route('admin.classes.show', $class) }}" class="btn-outline btn-sm">View</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p>No classes assigned</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
</x-app-layout>
