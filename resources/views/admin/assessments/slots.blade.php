<x-app-layout :title="'Assessment Slots'">
<div class="page-header">
    <div>
        <h1 class="page-title">Assessment Slots</h1>
        <p class="page-subtitle">Set weekly recurring times and one-off special dates.</p>
    </div>
    <a href="{{ route('admin.assessments.index') }}" class="btn-outline">← Back</a>
</div>

@if(session('success'))
<div class="alert alert-success mb-4 mx-4 lg:mx-0">{{ session('success') }}</div>
@endif

<div class="page-content">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ── Weekly Schedule ───────────────────────────────── --}}
        <div>
            <h2 class="text-base font-semibold text-navy mb-3">Weekly Schedule</h2>
            <div class="card">
                @php
                    $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                    $byDay = $availabilities->groupBy('day_of_week');
                @endphp

                @if($availabilities->isEmpty())
                <p class="text-sm text-gray-400 mb-4">No recurring times set yet.</p>
                @else
                <div class="space-y-4 mb-4">
                    @foreach($days as $dow => $dayName)
                        @if(isset($byDay[$dow]))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">{{ $dayName }}</p>
                            <div class="space-y-2">
                                @foreach($byDay[$dow] as $avail)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <div>
                                        <span class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($avail->start_time)->format('H:i') }}</span>
                                        <span class="text-xs text-gray-400 ml-2">max {{ $avail->max_bookings }} booking{{ $avail->max_bookings > 1 ? 's' : '' }}</span>
                                        @if($avail->notes)<span class="text-xs text-gray-400 ml-2">· {{ $avail->notes }}</span>@endif
                                    </div>
                                    <form method="POST" action="{{ route('admin.assessments.availabilities.delete', $avail) }}" onsubmit="return confirm('Remove this recurring slot?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                {{-- Add recurring slot form --}}
                <form method="POST" action="{{ route('admin.assessments.availabilities.store') }}" class="border-t border-gray-100 pt-4">
                    @csrf
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Add Recurring Time</p>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label">Day</label>
                            <select name="day_of_week" class="form-input" required>
                                @foreach($days as $dow => $dayName)
                                <option value="{{ $dow }}">{{ $dayName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-input" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label">Max Bookings</label>
                            <input type="number" name="max_bookings" value="1" min="1" max="20" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Venue / Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" name="notes" class="form-input" placeholder="e.g. Training ground A">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full">Add Recurring Slot</button>
                </form>
            </div>
        </div>

        {{-- ── Special Dates ─────────────────────────────────── --}}
        <div>
            <h2 class="text-base font-semibold text-navy mb-3">Special Dates</h2>
            <div class="card">
                @if($specialDates->isEmpty())
                <p class="text-sm text-gray-400 mb-4">No special dates added.</p>
                @else
                <div class="space-y-2 mb-4">
                    @foreach($specialDates as $special)
                    <div class="flex items-center justify-between p-3 bg-amber/5 rounded-xl border border-amber/20">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $special->date->format('D, d M Y') }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($special->start_time)->format('H:i') }} · max {{ $special->max_bookings }} booking{{ $special->max_bookings > 1 ? 's' : '' }}@if($special->notes) · {{ $special->notes }}@endif</p>
                        </div>
                        <form method="POST" action="{{ route('admin.assessments.special-dates.delete', $special) }}" onsubmit="return confirm('Remove this special date?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Add special date form --}}
                <form method="POST" action="{{ route('admin.assessments.special-dates.store') }}" class="border-t border-gray-100 pt-4">
                    @csrf
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Add Special Date</p>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-input" min="{{ today()->addDay()->toDateString() }}" required>
                        </div>
                        <div>
                            <label class="form-label">Start Time</label>
                            <input type="time" name="start_time" class="form-input" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label">Max Bookings</label>
                            <input type="number" name="max_bookings" value="1" min="1" max="20" class="form-input" required>
                        </div>
                        <div>
                            <label class="form-label">Venue / Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" name="notes" class="form-input" placeholder="e.g. Indoor arena">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full">Add Special Date</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Upcoming Preview ──────────────────────────────────── --}}
    <div class="mt-6">
        <h2 class="text-base font-semibold text-navy mb-3">Upcoming Slots Preview
            <span class="text-xs font-normal text-gray-400 ml-2">(next 8 weeks, admin calendar applied)</span>
        </h2>

        @if($preview->isEmpty())
        <div class="card text-center py-8">
            <p class="text-gray-400 text-sm">No slots will appear — add recurring times or special dates above.</p>
        </div>
        @else
        <div class="card divide-y divide-gray-100">
            @foreach($preview as $slot)
            <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                <div class="flex items-center gap-3">
                    @if($slot->source === 'special')
                    <span class="w-2 h-2 rounded-full bg-amber flex-shrink-0"></span>
                    @else
                    <span class="w-2 h-2 rounded-full bg-brand flex-shrink-0"></span>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $slot->date->format('D, d M Y') }} at {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</p>
                        @if($slot->notes)<p class="text-xs text-gray-400">{{ $slot->notes }}</p>@endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($slot->booked > 0)
                    <span class="text-xs text-gray-500">{{ $slot->booked }}/{{ $slot->max_bookings }} booked</span>
                    @endif
                    @if($slot->remaining === 0)
                    <span class="badge badge-completed">Full</span>
                    @else
                    <span class="badge badge-active">{{ $slot->remaining }} open</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mt-2">
            <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-brand inline-block"></span> Recurring</span>
            <span class="inline-flex items-center gap-1 ml-3"><span class="w-2 h-2 rounded-full bg-amber inline-block"></span> Special date</span>
        </p>
        @endif
    </div>

</div>
</x-app-layout>
