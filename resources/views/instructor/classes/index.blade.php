<x-app-layout :title="'My Classes'">
<div class="page-header"><h1 class="page-title">My Classes</h1></div>
<div class="page-content">
    @if($classes->isEmpty())
    <div class="empty-state"><div class="empty-state-icon"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><p class="text-gray-500">No classes assigned yet.</p></div>
    @else
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($classes as $class)
        <a href="{{ route('instructor.classes.show', $class) }}" class="card card-hover block">
            <div class="flex items-start justify-between gap-3">
                <div><p class="font-semibold text-navy text-lg">{{ $class->name }}</p><p class="text-sm text-gray-500 mt-0.5">{{ $class->classType?->name ?? '' }}</p></div>
                <span class="badge badge-{{ $class->status }}">{{ ucfirst($class->status) }}</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-2 text-xs text-gray-500">
                <div><p class="font-semibold text-gray-800 text-base">{{ $class->confirmedEnrolments->count() }}</p>Enrolled</div>
                @if($class->start_date)<div><p class="font-semibold text-gray-800">{{ $class->start_date->format('d M Y') }}</p>Start Date</div>@endif
                @if($class->has_final_exam)<div><p class="font-semibold text-amber">Yes</p>Final Exam</div>@endif
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
</x-app-layout>
