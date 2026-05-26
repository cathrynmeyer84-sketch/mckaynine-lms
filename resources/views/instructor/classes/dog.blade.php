<x-app-layout :title="$enrolment->dog->name">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="page-title">{{ $enrolment->dog->name }}</h1>
            <p class="page-subtitle">{{ $class->name }}</p>
        </div>
    </div>
</div>

@php $dog = $enrolment->dog; $handler = $enrolment->handler; @endphp

<div class="page-content space-y-4">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Dog info --}}
    <div class="card">
        <h2 class="font-semibold text-navy mb-4">Dog Info</h2>
        <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            @if($dog->breed)
            <div><span class="text-xs text-gray-400 block">Breed</span>{{ $dog->breed }}</div>
            @endif
            @if($dog->gender)
            <div><span class="text-xs text-gray-400 block">Gender</span>{{ ucfirst($dog->gender) }}</div>
            @endif
            @if($dog->date_of_birth)
            <div><span class="text-xs text-gray-400 block">Date of Birth</span>{{ $dog->date_of_birth->format('d M Y') }} <span class="text-gray-400">({{ $dog->date_of_birth->age }}y)</span></div>
            @endif
            @if($dog->spay_neuter_status)
            <div><span class="text-xs text-gray-400 block">Sterilised</span>{{ str_replace('_', ' ', ucfirst($dog->spay_neuter_status)) }}</div>
            @endif
            @if($dog->origin_story)
            <div><span class="text-xs text-gray-400 block">Where from</span>{{ $dog->origin_story }}</div>
            @endif
            @if($dog->age_when_acquired)
            <div><span class="text-xs text-gray-400 block">Age when acquired</span>{{ $dog->age_when_acquired }}</div>
            @endif
            @if($dog->vaccination_expiry_date)
            <div><span class="text-xs text-gray-400 block">Vaccination expiry</span>
                <span class="{{ $dog->vaccination_expiry_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                    {{ $dog->vaccination_expiry_date->format('d M Y') }}
                    @if($dog->vaccination_expiry_date->isPast()) · Expired @endif
                </span>
            </div>
            @endif
            @if($dog->training_goal)
            <div><span class="text-xs text-gray-400 block">Training goal</span>{{ str_replace('_', ' ', ucfirst($dog->training_goal)) }}</div>
            @endif
        </div>

        @if($dog->animal_buddies_at_home && count($dog->animal_buddies_at_home))
        <div class="mt-3"><span class="text-xs text-gray-400 block mb-1">Animal buddies at home</span>
            <div class="flex flex-wrap gap-1">
                @foreach($dog->animal_buddies_at_home as $b)<span class="badge text-xs">{{ $b }}</span>@endforeach
            </div>
        </div>
        @endif

        {{-- Socialisation --}}
        @if($dog->socialisation_other_dogs || $dog->socialisation_people || $dog->socialisation_other_animals)
        <div class="mt-4">
            <p class="text-xs text-gray-400 mb-2">Socialisation</p>
            <div class="flex flex-wrap gap-2 text-xs">
                @foreach(['socialisation_other_dogs' => 'Other Dogs', 'socialisation_other_animals' => 'Other Animals', 'socialisation_people' => 'People'] as $field => $label)
                @if($dog->$field)
                <span class="px-2 py-1 rounded-lg {{ $dog->$field === 'great' ? 'bg-green-50 text-green-700' : ($dog->$field === 'ok' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                    {{ $label }}: {{ ucfirst($dog->$field) }}
                </span>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Flags --}}
        @if($dog->behaviour_problems_details)
        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
            <p class="text-xs font-semibold text-amber-800 mb-0.5">Behaviour Note</p>
            <p class="text-sm text-amber-700">{{ $dog->behaviour_problems_details }}</p>
        </div>
        @endif
        @if($dog->health_issues)
        <div class="mt-3 bg-red-50 border border-red-200 rounded-xl px-3 py-2">
            <p class="text-xs font-semibold text-red-800 mb-0.5">Health Note</p>
            <p class="text-sm text-red-700">{{ $dog->health_issues }}</p>
        </div>
        @endif
    </div>

    {{-- Handler --}}
    <div class="card">
        <h2 class="font-semibold text-navy mb-3">Handler</h2>
        <div class="text-sm space-y-1">
            <p class="font-medium text-gray-900">{{ $handler->full_name }}</p>
            @if($handler->cell_number)<p class="text-gray-500">{{ $handler->cell_number }}</p>@endif
            @if($handler->user?->email)<p class="text-gray-500">{{ $handler->user->email }}</p>@endif
        </div>
    </div>

    {{-- Goals --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-navy">Goals</h2>
            <button type="button" onclick="document.getElementById('goalModal').style.display='flex'" class="btn btn-outline btn-sm">+ Add Goal</button>
        </div>

        @forelse($enrolment->goals as $goal)
        <div class="flex items-start justify-between gap-3 py-3 border-b border-gray-100 last:border-0">
            <div class="flex-1">
                <p class="text-sm text-gray-800">{{ $goal->goal }}</p>
                @if($goal->progress_notes)<p class="text-xs text-gray-400 mt-1">{{ $goal->progress_notes }}</p>@endif
                @if(!$goal->visible_to_handler)<p class="text-xs text-gray-300 mt-0.5">Not visible to handler</p>@endif
            </div>
            <span class="badge text-xs shrink-0 {{ $goal->status === 'achieved' ? 'badge-active' : 'badge-pending' }}">{{ ucfirst($goal->status) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">No goals added yet.</p>
        @endforelse
    </div>

    {{-- Attendance --}}
    @if($enrolment->registers->count())
    <div class="card">
        <h2 class="font-semibold text-navy mb-4">Attendance</h2>
        <div class="space-y-2">
            @foreach($enrolment->registers->sortBy('classDate.date') as $reg)
            @if($reg->classDate)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-700">Week {{ $reg->classDate->week_number }} — {{ $reg->classDate->date->format('d M Y') }}</span>
                <span class="badge text-xs {{ $reg->attendance === 'present' ? 'badge-active' : 'badge-pending' }}">
                    {{ ucfirst($reg->attendance) }}
                </span>
            </div>
            @if($reg->notes)<p class="text-xs text-gray-400 -mt-1 mb-1">{{ $reg->notes }}</p>@endif
            @endif
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Add Goal Modal --}}
<div id="goalModal" onclick="if(event.target===this)this.style.display='none'"
    style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div onclick="event.stopPropagation()"
        style="background:#fff;border-radius:1rem;padding:1.5rem;width:calc(100% - 2rem);max-width:28rem;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <h3 style="font-weight:600;color:#001d6d;margin-bottom:1rem;">Add Goal — {{ $dog->name }}</h3>
        <form method="POST" action="{{ route('instructor.classes.goals.store', $class) }}">
            @csrf
            <input type="hidden" name="enrolment_id" value="{{ $enrolment->id }}">
            <div style="margin-bottom:0.75rem;">
                <label style="display:block;font-size:0.75rem;font-weight:500;color:#374151;margin-bottom:0.25rem;">Goal</label>
                <textarea name="goal" rows="3" required placeholder="e.g. Improve loose-lead walking..."
                    style="width:100%;border:1px solid #d1d5db;border-radius:0.5rem;padding:0.5rem 0.75rem;font-size:0.875rem;resize:none;box-sizing:border-box;"></textarea>
            </div>
            <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;color:#4b5563;margin-bottom:0.75rem;cursor:pointer;">
                <input type="checkbox" name="visible_to_handler">
                Visible to handler in their portal
            </label>
            <div style="display:flex;gap:0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Save Goal</button>
                <button type="button" onclick="document.getElementById('goalModal').style.display='none'" class="btn btn-outline">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.appendChild(document.getElementById('goalModal'));
    });
</script>

</x-app-layout>
