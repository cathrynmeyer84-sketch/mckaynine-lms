<x-app-layout :title="$classType->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.class-types.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $classType->name }}</h1>
                <p class="page-subtitle">Class type details</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.class-types.edit', $classType) }}" class="btn-primary">Edit</a>
            <form action="{{ route('admin.class-types.destroy', $classType) }}" method="POST"
                onsubmit="return confirm('Delete {{ $classType->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-outline text-red-500 border-red-200 hover:bg-red-50">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Details + weekly content --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="card">
                <h2 class="form-section-title">Details</h2>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $classType->duration_type === 'term' ? 'badge-upcoming' : 'badge-active' }}">
                            {{ $classType->duration_label }}
                        </span>
                        @if($classType->has_structured_content)
                        <span class="badge badge-confirmed">Structured content</span>
                        @endif
                    </div>
                    @if($classType->description)
                    <p class="text-sm text-gray-600">{{ $classType->description }}</p>
                    @endif
                </div>
            </div>

            @if($classType->has_structured_content && $classType->weeks->count())
            <div class="space-y-3">
                <h2 class="font-semibold text-navy px-1">Weekly Content Template</h2>

                @foreach($classType->weeks as $week)
                <div class="card" x-data="{ open: false }">
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center justify-between text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand/10 flex items-center justify-center shrink-0">
                                <span class="text-sm font-bold text-brand">{{ $week->week_number }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Week {{ $week->week_number }}@if($week->title) — {{ $week->title }}@endif</p>
                                @if(!$week->title && !$week->description)
                                <p class="text-xs text-gray-400">No content added yet</p>
                                @endif
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-4 border-t border-gray-100 pt-4 space-y-3 text-sm">
                        @if($week->description)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Description</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $week->description }}</p>
                        </div>
                        @endif
                        @if($week->youtube_url)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Video</p>
                            <a href="{{ $week->youtube_url }}" target="_blank" class="text-brand underline break-all">{{ $week->youtube_url }}</a>
                        </div>
                        @endif
                        @if($week->practice_checklist)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Practice Checklist</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $week->practice_checklist }}</p>
                        </div>
                        @endif
                        @if($week->what_to_bring_next_week)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">What to Bring Next Week</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $week->what_to_bring_next_week }}</p>
                        </div>
                        @endif
                        @if($week->extra_notes)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Extra Notes</p>
                            <p class="text-gray-700 whitespace-pre-line">{{ $week->extra_notes }}</p>
                        </div>
                        @endif
                        @if(!$week->description && !$week->youtube_url && !$week->practice_checklist && !$week->what_to_bring_next_week && !$week->extra_notes)
                        <p class="text-gray-400 italic">No content added for this week.</p>
                        @endif
                        <div class="pt-2">
                            <a href="{{ route('admin.class-types.edit', $classType) }}#week-{{ $week->week_number }}"
                                class="text-xs text-brand underline">Edit this week's content →</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @elseif(!$classType->has_structured_content)
            <div class="card text-center py-8 text-gray-400 text-sm">
                This class type does not use structured weekly content.
            </div>
            @endif

        </div>

        {{-- Right: Classes using this type --}}
        <div class="space-y-4">
            <div class="card">
                <h2 class="form-section-title">Classes Using This Type</h2>
                @forelse($classType->classes as $class)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <a href="{{ route('admin.classes.show', $class) }}"
                            class="text-sm font-medium text-navy hover:text-brand">{{ $class->name }}</a>
                        <p class="text-xs text-gray-400">
                            @if($class->start_date){{ $class->start_date->format('d M Y') }}@else—@endif
                        </p>
                    </div>
                    <span class="badge badge-sm
                        {{ $class->status === 'active' ? 'badge-active' : ($class->status === 'upcoming' ? 'badge-upcoming' : 'badge') }}">
                        {{ ucfirst($class->status) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-400">No classes yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
</x-app-layout>
