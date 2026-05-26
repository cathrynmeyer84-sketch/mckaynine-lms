<x-app-layout title="Book a Private Lesson">
<div class="page-content">

    <div class="page-header">
        <div>
            <a href="{{ route('handler.private-lessons.index') }}" class="text-sm text-gray-400 hover:text-navy flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Private Lessons
            </a>
            <h1 class="page-title">Book a Private Lesson</h1>
        </div>
    </div>

    <div x-data="bookingWizard()" x-init="init()">

        {{-- Step indicator --}}
        <div class="flex items-center gap-2 mb-8">
            @foreach(['Choose Instructor', 'Pick a Slot', 'Confirm'] as $i => $stepLabel)
            <div class="flex items-center gap-2">
                @if($i > 0)<div class="w-8 h-px bg-gray-200"></div>@endif
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center transition-colors"
                        :class="{{ $i+1 }} <= step ? 'bg-navy text-white' : 'bg-gray-100 text-gray-400'">{{ $i+1 }}</div>
                    <span class="text-sm hidden sm:block" :class="{{ $i+1 }} === step ? 'font-semibold text-navy' : 'text-gray-400'">{{ $stepLabel }}</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Step 1: Choose Instructor --}}
        <div x-show="step === 1">
            <h2 class="text-base font-semibold text-navy mb-4">Choose an instructor</h2>

            @if($instructors->isEmpty())
            <div class="card text-center py-12">
                <p class="text-gray-400">No instructors are currently offering private lessons.</p>
            </div>
            @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($instructors as $instructor)
                <button type="button"
                    @click="selectInstructor({{ $instructor->id }}, '{{ addslashes($instructor->full_name) }}')"
                    class="card text-left hover:border-brand hover:shadow-md transition-all cursor-pointer border-2"
                    :class="instructorId === {{ $instructor->id }} ? 'border-brand' : 'border-transparent'">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl bg-navy text-white flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr($instructor->first_name, 0, 1) . substr($instructor->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-navy">{{ $instructor->full_name }}</p>
                            @if($instructor->private_lesson_bio)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-3">{{ $instructor->private_lesson_bio }}</p>
                            @endif
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Step 2: Pick a slot --}}
        <div x-show="step === 2" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-navy">Choose a time slot</h2>
                <button @click="step = 1; slots = []" class="text-sm text-gray-400 hover:text-navy flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
            </div>

            <div x-show="loadingSlots" class="card text-center py-12">
                <div class="inline-flex items-center gap-2 text-gray-400">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Loading available slots…
                </div>
            </div>

            <div x-show="!loadingSlots && slots.length === 0 && instructorId" class="card text-center py-12">
                <p class="text-gray-400">No available slots in the next 28 days.</p>
            </div>

            <div x-show="!loadingSlots && slots.length > 0" class="space-y-3">
                <template x-for="day in slots" :key="day.date">
                    <div class="card" x-data="{ open: true }">
                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between w-full text-left">
                            <p class="font-medium text-gray-800" x-text="day.date_label"></p>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" class="mt-3 flex flex-wrap gap-2">
                            <template x-for="slot in day.slots" :key="day.date + slot.start_time">
                                <button type="button"
                                    @click="selectSlot(day.date, slot.start_time, day.date_label, slot.start_time_label)"
                                    class="px-4 py-2 rounded-full text-sm font-medium border-2 transition-all"
                                    :class="selectedDate === day.date && selectedTime === slot.start_time
                                        ? 'bg-navy border-navy text-white'
                                        : 'border-gray-200 text-gray-700 hover:border-brand hover:text-brand'">
                                    <span x-text="slot.start_time_label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Step 3: Confirm --}}
        <div x-show="step === 3" x-cloak>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-navy">Confirm your booking</h2>
                <button @click="step = 2" class="text-sm text-gray-400 hover:text-navy flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </button>
            </div>

            <div class="card mb-4 bg-navy/5 border-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-navy/10 flex items-center justify-center">
                        <svg class="w-5 h-5 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="font-semibold text-navy" x-text="instructorName"></p>
                        <p class="text-sm text-gray-600"><span x-text="selectedDateLabel"></span> at <span x-text="selectedTimeLabel"></span> &mdash; 30 min</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('handler.private-lessons.store') }}" class="card">
                @csrf
                <input type="hidden" name="instructor_id" :value="instructorId">
                <input type="hidden" name="requested_date" :value="selectedDate">
                <input type="hidden" name="requested_start_time" :value="selectedTime">

                <div class="mb-4">
                    <label class="form-label">Which dog is this lesson for?</label>
                    <select name="dog_id" class="form-select mt-1" required>
                        <option value="">Select a dog…</option>
                        @foreach(auth()->user()->handler?->dogs ?? [] as $dog)
                        <option value="{{ $dog->id }}">{{ $dog->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="form-label">Notes for your instructor <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="handler_notes" rows="3" class="input mt-1"
                        placeholder="Any goals, areas to focus on, or things to know about your dog…" maxlength="500"></textarea>
                </div>

                <button type="submit" class="btn-primary w-full">Submit Lesson Request</button>
                <p class="text-xs text-gray-400 text-center mt-2">Your instructor will confirm the booking shortly.</p>
            </form>
        </div>

    </div>

</div>

<script>
function bookingWizard() {
    return {
        step: 1,
        instructorId: null,
        instructorName: '',
        selectedDate: null,
        selectedDateLabel: '',
        selectedTime: null,
        selectedTimeLabel: '',
        slots: [],
        loadingSlots: false,

        init() {},

        selectInstructor(id, name) {
            this.instructorId = id;
            this.instructorName = name;
            this.slots = [];
            this.selectedDate = null;
            this.selectedTime = null;
            this.step = 2;
            this.fetchSlots(id);
        },

        async fetchSlots(id) {
            this.loadingSlots = true;
            try {
                const res = await fetch(`/my/private-lessons/slots/${id}?t=${Date.now()}`, {
                    headers: { 'Cache-Control': 'no-cache' }
                });
                this.slots = await res.json();
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingSlots = false;
            }
        },

        selectSlot(date, time, dateLabel, timeLabel) {
            this.selectedDate = date;
            this.selectedTime = time;
            this.selectedDateLabel = dateLabel;
            this.selectedTimeLabel = timeLabel;
            this.step = 3;
        },
    };
}
</script>
</x-app-layout>
