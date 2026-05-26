<x-app-layout :title="'Register: ' . $class->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Class Register</h1>
                <p class="page-subtitle">{{ $class->name }}</p>
            </div>
        </div>
        <button onclick="window.print()" class="btn-outline hidden sm:flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Register
        </button>
    </div>

    @php
        $sessionDates = $class->dates->where('is_off_week', false)->sortBy('date');
    @endphp

    @if($class->confirmedEnrolments->count() && $sessionDates->count())
    <div class="card">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap sticky left-0 bg-white z-10 min-w-[180px]">
                            Handler / Dog
                        </th>
                        @foreach($sessionDates as $date)
                        <th class="py-3 px-2 text-center min-w-[80px]">
                            <div class="text-xs font-semibold text-gray-500 uppercase">W{{ $date->week_number }}</div>
                            <div class="text-xs text-gray-400 font-normal whitespace-nowrap">{{ $date->date->format('d M') }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($class->confirmedEnrolments as $enrolment)
                    @php
                        $attendedDateIds = $enrolment->registers->pluck('class_date_id')->toArray();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 sticky left-0 bg-white hover:bg-gray-50 z-10">
                            <div class="font-medium text-sm text-gray-900">
                                {{ $enrolment->handler?->first_name }} {{ $enrolment->handler?->last_name }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $enrolment->dog?->name }}</div>
                        </td>
                        @foreach($sessionDates as $date)
                        <td class="py-3 px-2 text-center">
                            @if(in_array($date->id, $attendedDateIds))
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-green-100">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @elseif(\Carbon\Carbon::parse($date->date)->isPast())
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-50">
                                    <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            @else
                                <div class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50">
                                    <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        <div class="px-4 py-3 border-t border-gray-100 flex flex-wrap gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-full bg-green-100 flex items-center justify-center"><svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>
                Attended
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-full bg-red-50 flex items-center justify-center"><svg class="w-2.5 h-2.5 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>
                Absent
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-4 h-4 rounded-full bg-gray-50 flex items-center justify-center"><div class="w-1.5 h-1.5 rounded-full bg-gray-200"></div></div>
                Upcoming
            </div>
        </div>
    </div>

    {{-- Summary stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <div class="card text-center">
            <div class="text-2xl font-bold text-navy">{{ $class->confirmedEnrolments->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Enrolled</div>
        </div>
        <div class="card text-center">
            <div class="text-2xl font-bold text-navy">{{ $sessionDates->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Sessions</div>
        </div>
        <div class="card text-center">
            <div class="text-2xl font-bold text-navy">{{ $sessionDates->where('date', '<', today()->toDateString())->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Completed</div>
        </div>
        <div class="card text-center">
            <div class="text-2xl font-bold text-navy">{{ $sessionDates->where('date', '>=', today()->toDateString())->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">Remaining</div>
        </div>
    </div>

    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <p>No enrolments or sessions to display</p>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
