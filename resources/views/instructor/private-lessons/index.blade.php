<x-app-layout title="Private Lessons">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Private Lessons</h1>
            <p class="page-subtitle">Manage your lesson requests and availability</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @php
        $defaultTab = $pending->count() > 0 ? 'pending' : ($upcoming->count() > 0 ? 'upcoming' : 'availability');
    @endphp

    <div x-data="{ tab: '{{ request('tab', $defaultTab) }}' }">

        {{-- Single tab bar --}}
        <div class="flex gap-1 border-b border-gray-200 mb-6 overflow-x-auto">
            <button @click="tab = 'pending'"
                :class="tab === 'pending' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors whitespace-nowrap">
                Pending
                @if($pending->count() > 0)
                <span class="ml-1.5 bg-amber text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $pending->count() }}</span>
                @endif
            </button>
            <button @click="tab = 'upcoming'"
                :class="tab === 'upcoming' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors whitespace-nowrap">
                Upcoming
            </button>
            <button @click="tab = 'past'"
                :class="tab === 'past' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors whitespace-nowrap">
                Past
            </button>
            <button @click="tab = 'availability'"
                :class="tab === 'availability' ? 'border-b-2 border-navy text-navy' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2.5 text-sm font-medium -mb-px transition-colors whitespace-nowrap">
                Availability
            </button>
        </div>

        {{-- ===== PENDING ===== --}}
        <div x-show="tab === 'pending'" x-cloak>
            @forelse($pending as $lesson)
            <div class="card mb-4" x-data="{ rescheduleOpen: false, rejectOpen: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-semibold text-navy">{{ $lesson->handler?->full_name }}</p>
                        <p class="text-sm text-gray-500">Dog: <span class="font-medium text-gray-700">{{ $lesson->dog?->name }}</span></p>
                    </div>
                    <span class="badge badge-pending shrink-0">Pending</span>
                </div>
                <div class="text-sm text-gray-600 mb-4">
                    <p>Requested: <span class="font-medium text-gray-800">{{ $lesson->requested_date?->format('l, d M Y') }}</span>
                        at <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</span>
                    </p>
                    @if($lesson->handler_notes)
                    <p class="mt-2 p-3 bg-gray-50 rounded-xl text-gray-700 italic">"{{ $lesson->handler_notes }}"</p>
                    @endif
                </div>

                <div class="flex gap-2 flex-wrap">
                    <form method="POST" action="{{ route('instructor.private-lessons.confirm', $lesson) }}">
                        @csrf
                        <button type="submit" class="btn-primary btn-sm">Confirm</button>
                    </form>
                    <button type="button" @click="rescheduleOpen = true" class="btn-outline btn-sm">Request Reschedule</button>
                    <button type="button" @click="rejectOpen = true" class="btn-outline btn-sm text-red-500 border-red-200 hover:bg-red-50">Reject</button>
                </div>

                {{-- Reschedule modal --}}
                <div x-show="rescheduleOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    @click.self="rescheduleOpen = false">
                    <div class="card w-full max-w-md" @click.stop>
                        <h3 class="font-semibold text-navy mb-3">Request Reschedule</h3>
                        <p class="text-sm text-gray-500 mb-4">Let {{ $lesson->handler?->first_name }} know why you'd like to reschedule.</p>
                        <form method="POST" action="{{ route('instructor.private-lessons.reschedule', $lesson) }}">
                            @csrf
                            <textarea name="reschedule_note" rows="3" class="input mb-4"
                                placeholder="e.g. I'm unavailable that day — please re-book for another slot." required maxlength="500"></textarea>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="rescheduleOpen = false" class="btn-outline btn-sm">Cancel</button>
                                <button type="submit" class="btn-primary btn-sm">Send Request</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Reject modal --}}
                <div x-show="rejectOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    @click.self="rejectOpen = false">
                    <div class="card w-full max-w-md" @click.stop>
                        <h3 class="font-semibold text-navy mb-3">Reject Lesson Request</h3>
                        <p class="text-sm text-gray-500 mb-4">Optionally add a note to {{ $lesson->handler?->first_name }}.</p>
                        <form method="POST" action="{{ route('instructor.private-lessons.reject', $lesson) }}">
                            @csrf
                            <textarea name="reason" rows="2" class="input mb-4"
                                placeholder="Optional reason…" maxlength="500"></textarea>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="rejectOpen = false" class="btn-outline btn-sm">Cancel</button>
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-xl px-4 py-2 text-sm font-medium transition-colors">Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="card text-center py-12">
                <p class="text-gray-400">No pending requests.</p>
            </div>
            @endforelse
        </div>

        {{-- ===== UPCOMING ===== --}}
        <div x-show="tab === 'upcoming'" x-cloak>
            @forelse($upcoming as $lesson)
            <div class="card mb-4" x-data="{ completeOpen: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="font-semibold text-navy">{{ $lesson->handler?->full_name }}</p>
                        <p class="text-sm text-gray-500">Dog: <span class="font-medium text-gray-700">{{ $lesson->dog?->name }}</span></p>
                    </div>
                    <span class="badge badge-confirmed shrink-0">Confirmed</span>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    <span class="font-medium text-gray-800">{{ $lesson->requested_date?->format('l, d M Y') }}</span>
                    at <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</span>
                </p>

                <button type="button" @click="completeOpen = true" class="btn-primary btn-sm">Mark Complete</button>

                {{-- Complete modal --}}
                <div x-show="completeOpen" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                    @click.self="completeOpen = false">
                    <div class="card w-full max-w-md" @click.stop>
                        <h3 class="font-semibold text-navy mb-3">Complete Lesson — {{ $lesson->dog?->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4">Add session notes to send to the handler (optional).</p>
                        <form method="POST" action="{{ route('instructor.private-lessons.complete', $lesson) }}">
                            @csrf
                            <textarea name="instructor_notes" rows="4" class="input mb-4"
                                placeholder="What did you work on? Any homework or follow-up notes for the handler…" maxlength="2000"></textarea>
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="completeOpen = false" class="btn-outline btn-sm">Cancel</button>
                                <button type="submit" class="btn-primary btn-sm">Complete &amp; Send Notes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="card text-center py-12">
                <p class="text-gray-400">No upcoming confirmed lessons.</p>
            </div>
            @endforelse
        </div>

        {{-- ===== PAST ===== --}}
        <div x-show="tab === 'past'" x-cloak>
            @forelse($past as $lesson)
            <div class="card mb-3">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-medium text-gray-800">{{ $lesson->handler?->full_name }}</p>
                            <span class="text-gray-300">·</span>
                            <p class="text-sm text-gray-500">{{ $lesson->dog?->name }}</p>
                        </div>
                        <p class="text-xs text-gray-400">{{ $lesson->requested_date?->format('d M Y') }} at {{ \Carbon\Carbon::parse($lesson->requested_start_time)->format('g:i A') }}</p>
                        @if($lesson->instructor_notes)
                        <p class="text-xs text-gray-500 mt-2 italic">"{{ \Str::limit($lesson->instructor_notes, 100) }}"</p>
                        @endif
                    </div>
                    <span class="badge {{ $lesson->status_badge_class }} shrink-0">{{ $lesson->status_label }}</span>
                </div>
            </div>
            @empty
            <div class="card text-center py-12">
                <p class="text-gray-400">No past lessons yet.</p>
            </div>
            @endforelse
        </div>

        {{-- ===== AVAILABILITY ===== --}}
        <div x-show="tab === 'availability'" x-cloak>

            {{-- Opt-in toggle --}}
            <div class="card mb-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="card-title">Offer Private Lessons</h2>
                        <p class="text-sm text-gray-500 mt-1">When enabled, handlers can find you and book 30-minute private sessions.</p>
                    </div>
                    <form method="POST" action="{{ route('instructor.private-lessons.opt-in') }}">
                        @csrf
                        <button type="submit"
                            class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none
                                {{ $instructor->private_lessons_enabled ? 'bg-brand' : 'bg-gray-300' }}">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform
                                {{ $instructor->private_lessons_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </form>
                </div>
            </div>

            @if($instructor->private_lessons_enabled)
            {{-- Weekly Availability --}}
            <div class="card mb-6" x-data="availabilityManager()" x-init="init()">
                <h2 class="card-title mb-1">Weekly Availability</h2>
                <p class="text-sm text-gray-500 mb-5">Set which time slots you are available each week. Handlers can book any available slot.</p>

                @php
                    $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    $existingSlots = $instructor->privateLessonAvailabilities->groupBy('day_of_week');
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
                    @foreach($dayNames as $dayIndex => $dayName)
                    <div class="border border-gray-200 rounded-xl p-3">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">{{ $dayName }}</p>

                        <div class="space-y-1 mb-2" id="slots-day-{{ $dayIndex }}">
                            @foreach($existingSlots->get($dayIndex, collect()) as $slot)
                            <div class="flex items-center gap-1" id="slot-{{ $slot->id }}">
                                <span class="text-xs bg-navy/5 text-navy px-2 py-0.5 rounded-full">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                                </span>
                                <button type="button" @click="removeSlotFromUI({{ $dayIndex }}, '{{ $slot->start_time }}', {{ $slot->id }})"
                                    class="text-gray-300 hover:text-red-400 transition-colors ml-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex gap-1">
                            <input type="time" x-model="newTimes[{{ $dayIndex }}]"
                                class="input text-xs py-1 px-2 flex-1 min-w-0" step="1800">
                            <button type="button" @click="addSlot({{ $dayIndex }})"
                                class="btn-primary btn-sm px-2 text-xs shrink-0">+</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('instructor.private-lessons.availability.save') }}" id="save-availability-form">
                    @csrf
                    <div id="slots-hidden-inputs"></div>
                    <button type="submit" class="btn-primary">Save Availability</button>
                </form>
            </div>

            {{-- Blocked Dates --}}
            <div class="card">
                <h2 class="card-title mb-1">Blocked Dates</h2>
                <p class="text-sm text-gray-500 mb-5">Add specific dates when you are unavailable for private lessons.</p>

                <form method="POST" action="{{ route('instructor.private-lessons.blocks.store') }}" class="flex gap-3 flex-wrap items-end mb-6">
                    @csrf
                    <div>
                        <label class="form-label">Date</label>
                        <input type="date" name="blocked_date" class="input mt-1" min="{{ today()->addDay()->toDateString() }}" required>
                    </div>
                    <div class="flex-1 min-w-[180px]">
                        <label class="form-label">Reason <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="reason" class="input mt-1" placeholder="e.g. Holiday" maxlength="200">
                    </div>
                    <button type="submit" class="btn-primary shrink-0">Add Blocked Date</button>
                </form>

                @if($instructor->privateLessonBlocks->count())
                <div class="divide-y divide-gray-100">
                    @foreach($instructor->privateLessonBlocks->sortBy('blocked_date') as $block)
                    <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $block->blocked_date->format('l, d M Y') }}</p>
                            @if($block->reason)<p class="text-xs text-gray-400">{{ $block->reason }}</p>@endif
                        </div>
                        <form method="POST" action="{{ route('instructor.private-lessons.blocks.destroy', $block) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors"
                                onclick="return confirm('Remove this blocked date?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">No blocked dates added yet.</p>
                @endif
            </div>
            @endif

        </div>

    </div>

</div>

<script>
function availabilityManager() {
    return {
        slots: @json(
            $instructor->privateLessonAvailabilities->map(fn($s) => ['day_of_week' => $s->day_of_week, 'start_time' => $s->start_time])->values()
        ),
        newTimes: { 0: '', 1: '', 2: '', 3: '', 4: '', 5: '', 6: '' },

        init() { this.renderHiddenInputs(); },

        addSlot(day) {
            const time = this.newTimes[day];
            if (!time) return;
            if (this.slots.some(s => s.day_of_week == day && s.start_time === time)) {
                alert('That slot already exists.'); return;
            }
            this.slots.push({ day_of_week: day, start_time: time });
            this.newTimes[day] = '';
            this.renderHiddenInputs();
            this.renderSlotChip(day, time);
        },

        removeSlotFromUI(day, time, id) {
            this.slots = this.slots.filter(s => !(s.day_of_week == day && s.start_time === time));
            this.renderHiddenInputs();
            const el = document.getElementById('slot-' + id);
            if (el) el.remove();
        },

        renderHiddenInputs() {
            const container = document.getElementById('slots-hidden-inputs');
            if (!container) return;
            container.innerHTML = '';
            this.slots.forEach((slot, i) => {
                container.innerHTML += `<input type="hidden" name="slots[${i}][day_of_week]" value="${slot.day_of_week}">
                    <input type="hidden" name="slots[${i}][start_time]" value="${slot.start_time}">`;
            });
        },

        renderSlotChip(day, time) {
            const container = document.getElementById('slots-day-' + day);
            if (!container) return;
            const label = new Date('1970-01-01T' + time).toLocaleTimeString('en-ZA', { hour: 'numeric', minute: '2-digit' });
            const id = 'new-' + day + '-' + time.replace(':', '');
            const div = document.createElement('div');
            div.className = 'flex items-center gap-1';
            div.id = id;
            div.innerHTML = `<span class="text-xs bg-navy/5 text-navy px-2 py-0.5 rounded-full">${label}</span>
                <button type="button" onclick="document.getElementById('${id}').remove(); Alpine.store && null;"
                    @click="removeNewSlot(${day}, '${time}')"
                    class="text-gray-300 hover:text-red-400 transition-colors ml-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>`;
            container.appendChild(div);
        },

        removeNewSlot(day, time) {
            this.slots = this.slots.filter(s => !(s.day_of_week == day && s.start_time === time));
            this.renderHiddenInputs();
        },
    };
}
</script>
</x-app-layout>
