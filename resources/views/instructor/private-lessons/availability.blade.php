<x-app-layout title="Private Lesson Availability">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Private Lessons</h1>
            <p class="page-subtitle">Manage your availability and opt-in settings</p>
        </div>
        <a href="{{ route('instructor.private-lessons.requests') }}" class="btn-outline">View Requests</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    {{-- Opt-in toggle --}}
    <div class="card mb-6" x-data>
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

        @if($instructor->private_lessons_enabled)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <form method="POST" action="{{ route('instructor.private-lessons.availability.save') }}">
                @csrf
                <label class="form-label">Your Bio <span class="text-gray-400 font-normal">(shown to handlers when booking)</span></label>
                <textarea name="private_lesson_bio" rows="3" class="input mt-1" placeholder="Tell handlers about your specialisms and experience…">{{ old('private_lesson_bio', $instructor->private_lesson_bio) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Max 1000 characters.</p>
                <button type="submit" class="btn-primary mt-3">Save Bio</button>
            </form>
        </div>
        @endif
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

                {{-- Existing slots --}}
                <div class="space-y-1 mb-2" id="slots-day-{{ $dayIndex }}">
                    @foreach($existingSlots->get($dayIndex, collect()) as $slot)
                    <div class="flex items-center gap-1" id="slot-{{ $slot->id }}">
                        <span class="text-xs bg-navy/5 text-navy px-2 py-0.5 rounded-full">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }}
                        </span>
                        <form method="POST" action="{{ route('instructor.private-lessons.availability.save') }}" x-data @submit.prevent="removeSlot($event, {{ $slot->id }})">
                            @csrf
                            <button type="button" @click="removeSlotFromUI({{ $dayIndex }}, '{{ $slot->start_time }}', {{ $slot->id }})"
                                class="text-gray-300 hover:text-red-400 transition-colors ml-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>

                {{-- Add new slot --}}
                <div class="flex gap-1">
                    <input type="time" x-model="newTimes[{{ $dayIndex }}]"
                        class="input text-xs py-1 px-2 flex-1 min-w-0" step="1800">
                    <button type="button" @click="addSlot({{ $dayIndex }})"
                        class="btn-primary btn-sm px-2 text-xs shrink-0">+</button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Hidden form to save all slots --}}
        <form method="POST" action="{{ route('instructor.private-lessons.availability.save') }}" id="save-availability-form">
            @csrf
            <input type="hidden" name="private_lesson_bio" value="{{ $instructor->private_lesson_bio }}">
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
                    @if($block->reason)
                    <p class="text-xs text-gray-400">{{ $block->reason }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('instructor.private-lessons.blocks.destroy', $block) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors" onclick="return confirm('Remove this blocked date?')">
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

<script>
function availabilityManager() {
    return {
        slots: @json(
            $instructor->privateLessonAvailabilities->map(fn($s) => ['day_of_week' => $s->day_of_week, 'start_time' => $s->start_time])->values()
        ),
        newTimes: { 0: '', 1: '', 2: '', 3: '', 4: '', 5: '', 6: '' },

        init() {
            this.renderHiddenInputs();
        },

        addSlot(day) {
            const time = this.newTimes[day];
            if (!time) return;

            // Check duplicate
            const exists = this.slots.some(s => s.day_of_week == day && s.start_time === time);
            if (exists) { alert('That slot already exists.'); return; }

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
            const div = document.createElement('div');
            div.className = 'flex items-center gap-1';
            div.innerHTML = `<span class="text-xs bg-navy/5 text-navy px-2 py-0.5 rounded-full">${label}</span>
                <button type="button" onclick="this.closest('[x-data]').__x.$data.removeNewSlot(${day}, '${time}', this.closest('.flex'))"
                    class="text-gray-300 hover:text-red-400 transition-colors ml-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>`;
            container.appendChild(div);
        },

        removeNewSlot(day, time, el) {
            this.slots = this.slots.filter(s => !(s.day_of_week == day && s.start_time === time));
            this.renderHiddenInputs();
            el.remove();
        },
    };
}
</script>
</x-app-layout>
