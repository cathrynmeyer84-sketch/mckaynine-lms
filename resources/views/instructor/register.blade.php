<x-app-layout :title="'Register — Week ' . $classDate->week_number">
<div class="page-header">
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.classes.show', $class) }}" class="text-gray-400 hover:text-navy"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
        <div>
            <h1 class="page-title">Week {{ $classDate->week_number }} Register</h1>
            <p class="page-subtitle">{{ $class->name }} — {{ $classDate->date->format('D, d M Y') }}</p>
        </div>
    </div>
</div>
<div class="page-content">
    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('instructor.register.store', [$class, $classDate]) }}">
        @csrf
        <div class="space-y-3 mb-6">
            @forelse($enrolments as $enrolment)
            @php $reg = $registers->get($enrolment->id); $isPresent = $reg?->attendance === 'present'; @endphp
            <div class="card" x-data="{ present: {{ $isPresent ? 'true' : 'false' }}, notesOpen: {{ $reg?->notes ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-brand/10 flex items-center justify-center shrink-0 text-brand font-bold text-sm">
                            {{ strtoupper(substr($enrolment->dog->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $enrolment->dog->name }}</p>
                            <p class="text-xs text-gray-500">{{ $enrolment->handler->full_name }}</p>
                        </div>
                    </div>

                    {{-- Yes / No toggle --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" @click="present = false"
                            :class="!present ? 'bg-red-100 text-red-700 border-red-300' : 'bg-white text-gray-400 border-gray-200'"
                            class="border rounded-xl px-4 py-1.5 text-sm font-semibold transition-all">
                            Absent
                        </button>
                        <button type="button" @click="present = true"
                            :class="present ? 'bg-green-100 text-green-700 border-green-300' : 'bg-white text-gray-400 border-gray-200'"
                            class="border rounded-xl px-4 py-1.5 text-sm font-semibold transition-all">
                            Present
                        </button>
                        <input type="hidden" name="attendance[{{ $enrolment->id }}]" :value="present ? 'present' : 'absent'">
                    </div>
                </div>

                {{-- Notes toggle --}}
                <div class="mt-2">
                    <button type="button" @click="notesOpen = !notesOpen" class="text-xs text-gray-400 hover:text-gray-600">
                        <span x-text="notesOpen ? '− Hide notes' : '+ Add note'"></span>
                    </button>
                    <textarea x-show="notesOpen" x-cloak
                        name="notes[{{ $enrolment->id }}]"
                        class="form-textarea text-sm mt-2 w-full" rows="2"
                        placeholder="e.g. struggled with recall, great improvement on sit...">{{ $reg?->notes }}</textarea>
                </div>
            </div>
            @empty
            <div class="empty-state"><p class="text-gray-500">No dogs enrolled in this class.</p></div>
            @endforelse
        </div>

        @if($enrolments->count() > 0)
        <button type="submit" class="btn btn-primary w-full">Save Register</button>
        @endif
    </form>
</div>
</x-app-layout>
