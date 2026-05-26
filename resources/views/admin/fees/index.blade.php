<x-app-layout :title="'Instructor Fees'">
<div class="page-content">

    @if(session('success'))
    <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">Instructor Fees</h1>
            <p class="page-subtitle">40% rate · 25% multi-dog discount applied where flagged</p>
        </div>
    </div>

    {{-- Term selector --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('admin.fees.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="form-label">Term</label>
                <select name="term" onchange="this.form.submit()" class="form-input">
                    @foreach($termOptions as $opt)
                    <option value="{{ $opt['value'] }}" {{ $selectedTerm === $opt['value'] ? 'selected' : '' }}>
                        {{ $opt['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-gray-400 self-center pb-0.5">
                {{ $periodStart->format('d M Y') }} – {{ $periodEnd->format('d M Y') }}
                &nbsp;·&nbsp; term classes + monthly classes in this period
            </p>
        </form>
    </div>

    @php
        $hasTermly  = collect($termlyResults)->filter(fn($r) => count($r['lines']) > 0)->isNotEmpty();
        $hasMonthly = collect($monthlyResults)->filter(fn($r) => $r['total'] > 0)->isNotEmpty();
        $hasAny     = $hasTermly || $hasMonthly;
    @endphp

    @if($hasAny)
    <div class="space-y-8">

        {{-- ══════════════════════════════════════════
             TERMLY INSTRUCTORS
             ══════════════════════════════════════════ --}}
        @if($hasTermly)
        <div>
            @if($hasMonthly)
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Termly — paid once per term</h2>
            @endif

            <div class="space-y-5">
            @foreach($termlyResults as $row)
            @if(count($row['lines']) > 0)
            @php $stmt = $statements[$row['instructor']->id] ?? null; @endphp
            <div class="card">

                {{-- Instructor header --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">{{ substr($row['instructor']->first_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h2 class="font-semibold text-navy">{{ $row['instructor']->full_name }}</h2>
                            @if($row['instructor']->email)
                            <p class="text-xs text-gray-400">{{ $row['instructor']->email }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Term Total</p>
                        <p class="text-xl font-bold text-navy">R {{ number_format($row['total'], 2) }}</p>
                    </div>
                </div>

                {{-- Line items --}}
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Type</th>
                            <th>Detail</th>
                            <th class="text-right">Fee (R)</th>
                        </tr>
                    </thead>
                    @foreach($row['lines'] as $line)
                    @php $hasDogs = count($line['dogs'] ?? []) > 0; @endphp
                    <tbody x-data="{ open: false }">
                        <tr>
                            <td class="font-medium text-gray-900">
                                {{ $line['class']?->name ?? 'Private Lessons' }}
                                @if($hasDogs)
                                <button type="button" @click="open = !open"
                                    class="ml-1.5 inline-flex items-center text-brand hover:text-navy transition-colors"
                                    :title="open ? 'Hide dogs' : 'Show dogs'">
                                    <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="open ? 'rotate-90' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                @endif
                            </td>
                            <td>@include('admin.fees._type-badge', ['type' => $line['type']])</td>
                            <td class="text-sm text-gray-500">{{ $line['detail'] }}</td>
                            <td class="text-right font-semibold text-navy">R {{ number_format($line['fee'], 2) }}</td>
                        </tr>
                        @if($hasDogs)
                        <tr x-show="open" x-transition style="display:none;">
                            <td colspan="4" class="p-0">
                                <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="text-gray-400 uppercase tracking-wide">
                                                <th class="text-left pb-1.5 font-medium">Dog</th>
                                                @if($line['type'] === 'term')
                                                <th class="text-center pb-1.5 font-medium">Sessions</th>
                                                @else
                                                <th class="text-center pb-1.5 font-medium">Months</th>
                                                @endif
                                                <th class="text-right pb-1.5 font-medium">Fee (R)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($line['dogs'] as $dog)
                                            <tr>
                                                <td class="py-1.5 pr-4 font-medium text-gray-800">
                                                    {{ $dog['name'] }}
                                                    @if($dog['discount'])
                                                    <span class="ml-1 text-amber-600 font-normal">25% multi-dog</span>
                                                    @endif
                                                </td>
                                                @if($line['type'] === 'term')
                                                <td class="py-1.5 text-center text-gray-500">{{ $dog['attended'] }} / {{ $dog['total'] }}</td>
                                                @else
                                                <td class="py-1.5 text-center text-gray-500">{{ $dog['months'] }}</td>
                                                @endif
                                                <td class="py-1.5 text-right font-semibold text-navy">R {{ number_format($dog['fee'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                    @endforeach
                </table>

                @include('admin.fees._statement-actions', [
                    'stmt'         => $stmt,
                    'instructorId' => $row['instructor']->id,
                    'termKey'      => $selectedTerm,
                ])
            </div>
            @endif
            @endforeach
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════
             MONTHLY INSTRUCTORS
             ══════════════════════════════════════════ --}}
        @if($hasMonthly)
        <div>
            @if($hasTermly)
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Monthly — paid each month</h2>
            @endif

            <div class="space-y-5">
            @foreach($monthlyResults as $monthData)
            @if($monthData['total'] > 0)
            <div class="card">

                {{-- Instructor header --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-sm">{{ substr($monthData['instructor']->first_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h2 class="font-semibold text-navy">{{ $monthData['instructor']->full_name }}</h2>
                            @if($monthData['instructor']->email)
                            <p class="text-xs text-gray-400">{{ $monthData['instructor']->email }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Term Total</p>
                        <p class="text-xl font-bold text-navy">R {{ number_format($monthData['total'], 2) }}</p>
                    </div>
                </div>

                {{-- One section per month --}}
                @foreach($monthData['months'] as $monthKey => $month)
                @php
                    $mStmt = $monthStatements[$monthData['instructor']->id][$monthKey] ?? null;
                @endphp
                <div class="{{ !$loop->first ? 'mt-5 pt-5 border-t border-gray-100' : '' }}">

                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-sm text-navy">{{ $month['label'] }}</h3>
                        <span class="font-bold text-sm text-navy">R {{ number_format($month['total'], 2) }}</span>
                    </div>

                    @if(count($month['lines']) > 0)
                    <table class="data-table text-sm">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Type</th>
                                <th>Detail</th>
                                <th class="text-right">Fee (R)</th>
                            </tr>
                        </thead>
                        @foreach($month['lines'] as $line)
                        @php $hasDogs = count($line['dogs'] ?? []) > 0; @endphp
                        <tbody x-data="{ open: false }">
                            <tr>
                                <td class="font-medium text-gray-900">
                                    {{ $line['class']?->name ?? 'Private Lessons' }}
                                    @if($hasDogs)
                                    <button type="button" @click="open = !open"
                                        class="ml-1.5 inline-flex items-center text-brand hover:text-navy transition-colors"
                                        :title="open ? 'Hide dogs' : 'Show dogs'">
                                        <svg class="w-3.5 h-3.5 transition-transform duration-150" :class="open ? 'rotate-90' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    @endif
                                </td>
                                <td>@include('admin.fees._type-badge', ['type' => $line['type']])</td>
                                <td class="text-gray-500">{{ $line['detail'] }}</td>
                                <td class="text-right font-semibold text-navy">R {{ number_format($line['fee'], 2) }}</td>
                            </tr>
                            @if($hasDogs)
                            <tr x-show="open" x-transition style="display:none;">
                                <td colspan="4" class="p-0">
                                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                                        <table class="w-full text-xs">
                                            <thead>
                                                <tr class="text-gray-400 uppercase tracking-wide">
                                                    <th class="text-left pb-1.5 font-medium">Dog</th>
                                                    @if($line['type'] === 'term')
                                                    <th class="text-center pb-1.5 font-medium">Sessions</th>
                                                    @else
                                                    <th class="text-center pb-1.5 font-medium">Months</th>
                                                    @endif
                                                    <th class="text-right pb-1.5 font-medium">Fee (R)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($line['dogs'] as $dog)
                                                <tr>
                                                    <td class="py-1.5 pr-4 font-medium text-gray-800">
                                                        {{ $dog['name'] }}
                                                        @if($dog['discount'])
                                                        <span class="ml-1 text-amber-600 font-normal">25% multi-dog</span>
                                                        @endif
                                                    </td>
                                                    @if($line['type'] === 'term')
                                                    <td class="py-1.5 text-center text-gray-500">{{ $dog['attended'] }} / {{ $dog['total'] }}</td>
                                                    @else
                                                    <td class="py-1.5 text-center text-gray-500">{{ $dog['months'] }}</td>
                                                    @endif
                                                    <td class="py-1.5 text-right font-semibold text-navy">R {{ number_format($dog['fee'], 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                        @endforeach
                    </table>
                    @else
                    <p class="text-xs text-gray-400 italic">No fee activity this month.</p>
                    @endif

                    @include('admin.fees._statement-actions', [
                        'stmt'         => $mStmt,
                        'instructorId' => $monthData['instructor']->id,
                        'termKey'      => $monthKey,
                    ])
                </div>
                @endforeach

            </div>
            @endif
            @endforeach
            </div>
        </div>
        @endif

        {{-- Grand total --}}
        <div class="card bg-navy text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-80">
                        Total payable — {{ collect($termOptions)->firstWhere('value', $selectedTerm)['label'] ?? $selectedTerm }}
                    </p>
                    <p class="text-xs opacity-60 mt-0.5">All active instructors combined</p>
                </div>
                <p class="text-3xl font-bold">R {{ number_format($grandTotal, 2) }}</p>
            </div>
        </div>

    </div>

    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p>No fee data for this period</p>
            <p class="text-sm text-gray-400 mt-1">Ensure instructors are assigned to enrolments and classes are active during this term.</p>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
