<x-app-layout :title="$statement->period_label">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('instructor.fees.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $statement->period_label }}</h1>
                <p class="page-subtitle">Fee breakdown · {{ $statement->period_start->format('d M') }} – {{ $statement->period_end->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Status banner --}}
    @if($statement->is_paid)
    <div class="rounded-xl bg-green-50 border border-green-200 px-4 py-3 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-green-800 font-medium">Paid on {{ $statement->paid_at->format('d M Y') }}</p>
    </div>
    @else
    <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 mb-6 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-amber-800 font-medium">Payment pending</p>
    </div>
    @endif

    {{-- Line items with per-dog breakdowns --}}
    <div class="space-y-4">

        @foreach($statement->lines as $line)
        @php
            $classId   = $line['class_id'] ?? null;
            $breakdown = $classId ? ($dogBreakdowns[$classId] ?? null) : null;
            $hasDogs   = $breakdown && count($breakdown['dogs'] ?? []) > 0;
        @endphp
        <div class="card">

            {{-- Class header --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-semibold text-navy">{{ $line['class_name'] ?? 'Private Lessons' }}</h3>
                        @if($line['type'] === 'term')
                            <span class="badge badge-active">Term</span>
                        @elseif($line['type'] === 'ongoing')
                            <span class="badge badge-pending">Monthly</span>
                        @else
                            <span class="badge">Private</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $line['detail'] }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Fee</p>
                    <p class="font-bold text-navy">R {{ number_format($line['fee'], 2) }}</p>
                </div>
            </div>

            {{-- Per-dog breakdown (collapsible) --}}
            @if($hasDogs)
            <div x-data="{ open: false }" class="mt-3 pt-3 border-t border-gray-100">
                <button type="button" @click="open = !open"
                    class="flex items-center gap-1.5 text-xs font-medium text-brand hover:text-navy transition-colors">
                    <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="open ? 'rotate-90' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span x-text="open ? 'Hide dog breakdown' : 'Show dog breakdown ({{ count($breakdown[\'dogs\']) }})'"></span>
                </button>

                <div x-show="open" x-transition class="mt-3">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 uppercase tracking-wide">
                                <th class="text-left pb-2 font-medium">Dog</th>
                                @if($breakdown['type'] === 'term')
                                <th class="text-center pb-2 font-medium">Sessions</th>
                                @else
                                <th class="text-center pb-2 font-medium">Months</th>
                                @endif
                                <th class="text-right pb-2 font-medium">Fee (R)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($breakdown['dogs'] as $dog)
                            <tr>
                                <td class="py-2 pr-4">
                                    <span class="font-medium text-gray-900">{{ $dog['name'] }}</span>
                                    @if($dog['discount'])
                                    <span class="ml-1.5 text-xs text-amber-600 font-medium">25% multi-dog</span>
                                    @endif
                                </td>
                                @if($breakdown['type'] === 'term')
                                <td class="py-2 text-center text-gray-500">{{ $dog['attended'] }} / {{ $dog['total'] }}</td>
                                @else
                                <td class="py-2 text-center text-gray-500">{{ $dog['months'] }}</td>
                                @endif
                                <td class="py-2 text-right font-semibold text-navy">R {{ number_format($dog['fee'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
        @endforeach

        {{-- Total --}}
        <div class="card bg-navy text-white">
            <div class="flex items-center justify-between">
                <p class="font-semibold opacity-90">Total</p>
                <p class="text-2xl font-bold">R {{ number_format($statement->total, 2) }}</p>
            </div>
        </div>

    </div>

    @if($statement->is_paid && $statement->payment_notes)
    <div class="card mt-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Payment Notes</p>
        <p class="text-sm text-gray-700">{{ $statement->payment_notes }}</p>
    </div>
    @endif

</div>
</x-app-layout>
