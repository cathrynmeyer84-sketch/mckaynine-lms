<x-app-layout :title="'Exam Results'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Exam Results</h1>
            <p class="page-subtitle">Manage and release handler results</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'awaiting' }">

        <div class="flex border-b border-gray-200 mb-6 gap-1">
            <button @click="tab = 'awaiting'"
                :class="tab === 'awaiting' ? 'border-b-2 border-brand text-brand' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm font-medium flex items-center gap-2 transition-colors">
                Awaiting Release
                @if($submitted->count())
                    <span class="bg-amber text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">{{ $submitted->count() }}</span>
                @endif
            </button>
            <button @click="tab = 'released'"
                :class="tab === 'released' ? 'border-b-2 border-brand text-brand' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm font-medium transition-colors">
                Released
                <span class="text-gray-400 text-xs ml-1">({{ $released->count() }})</span>
            </button>
        </div>

        {{-- Awaiting Release Tab --}}
        <div x-show="tab === 'awaiting'">
            @if($submitted->count())
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Handler</th>
                                <th>Dog</th>
                                <th>Class</th>
                                <th>Score</th>
                                <th>Achievement</th>
                                <th>Submitted</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submitted as $result)
                            <tr class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('admin.results.show', $result) }}'">
                                <td>
                                    <div class="font-medium text-gray-900">
                                        {{ $result->enrolment?->handler?->first_name }}
                                        {{ $result->enrolment?->handler?->last_name }}
                                    </div>
                                </td>
                                <td class="text-sm text-gray-600">{{ $result->enrolment?->dog?->name ?? '—' }}</td>
                                <td class="text-sm text-gray-600">{{ $result->enrolment?->dogClass?->name ?? '—' }}</td>
                                <td>
                                    @if($result->total_score !== null)
                                    <span class="font-semibold text-gray-900">{{ number_format($result->total_score, 1) }}%</span>
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($result->achievement_level)
                                    @php
                                        $achClass = match($result->achievement_level) {
                                            'merit_pass' => 'bg-green-50 text-green-700',
                                            'pass'       => 'bg-blue-50 text-blue-700',
                                            'review'     => 'bg-amber/10 text-amber',
                                            default      => 'bg-red-50 text-red-600',
                                        };
                                    @endphp
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $achClass }}">
                                        {{ ucwords(str_replace('_', ' ', $result->achievement_level)) }}
                                    </span>
                                    @else
                                    <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="text-sm text-gray-500">{{ $result->submitted_at?->format('d M Y') ?? $result->created_at?->format('d M Y') }}</td>
                                <td onclick="event.stopPropagation()">
                                    <a href="{{ route('admin.results.show', $result) }}" class="btn-primary btn-sm">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p>No results awaiting release</p>
                    <p class="text-sm text-gray-400 mt-1">All submitted results have been released</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Released Tab --}}
        <div x-show="tab === 'released'">
            @if($released->count())
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Handler</th>
                                <th>Dog</th>
                                <th>Class</th>
                                <th>Score</th>
                                <th>Achievement</th>
                                <th>Released</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($released as $result)
                            <tr class="cursor-pointer hover:bg-gray-50" onclick="window.location='{{ route('admin.results.show', $result) }}'">
                                <td>
                                    <div class="font-medium text-gray-900">
                                        {{ $result->enrolment?->handler?->first_name }}
                                        {{ $result->enrolment?->handler?->last_name }}
                                    </div>
                                </td>
                                <td class="text-sm text-gray-600">{{ $result->enrolment?->dog?->name ?? '—' }}</td>
                                <td class="text-sm text-gray-600">{{ $result->enrolment?->dogClass?->name ?? '—' }}</td>
                                <td>
                                    @if($result->total_score !== null)
                                    <span class="font-semibold text-gray-900">{{ number_format($result->total_score, 1) }}%</span>
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($result->achievement_level)
                                    @php
                                        $achClass = match($result->achievement_level) {
                                            'merit_pass' => 'bg-green-50 text-green-700',
                                            'pass'       => 'bg-blue-50 text-blue-700',
                                            'review'     => 'bg-amber/10 text-amber',
                                            default      => 'bg-red-50 text-red-600',
                                        };
                                    @endphp
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $achClass }}">
                                        {{ ucwords(str_replace('_', ' ', $result->achievement_level)) }}
                                    </span>
                                    @else
                                    <span class="text-gray-400 text-sm">—</span>
                                    @endif
                                </td>
                                <td class="text-sm text-gray-500">{{ $result->released_at?->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <p>No released results yet</p>
                </div>
            </div>
            @endif
        </div>

    </div>

</div>
</x-app-layout>
