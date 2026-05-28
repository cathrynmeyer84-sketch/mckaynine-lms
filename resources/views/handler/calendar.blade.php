@extends('layouts.app')

@section('title', 'School Calendar')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">School Calendar</h1>
        <p class="page-subtitle">Upcoming class schedule and no-class days</p>
    </div>
    {{-- Year navigation --}}
    @if($prevYear || $nextYear || $schoolYear)
    <div class="flex items-center gap-2">
        @if($prevYear)
        <a href="{{ route('handler.calendar', ['year' => $prevYear->id]) }}"
           class="btn btn-outline btn-sm">← {{ $prevYear->label }}</a>
        @else
        <span class="btn btn-outline btn-sm opacity-30 cursor-default">←</span>
        @endif

        <span class="text-sm font-semibold text-navy px-2">{{ $schoolYear?->label ?? 'Calendar' }}</span>

        @if($nextYear)
        <a href="{{ route('handler.calendar', ['year' => $nextYear->id]) }}"
           class="btn btn-outline btn-sm">{{ $nextYear->label }} →</a>
        @else
        <span class="btn btn-outline btn-sm opacity-30 cursor-default">→</span>
        @endif
    </div>
    @endif
</div>

<div class="page-content space-y-8">

    {{-- Legend --}}
    <div class="flex flex-wrap gap-4 text-xs">
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded border border-navy/20 bg-white inline-block"></span>
            Normal day
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded border border-brand bg-brand/10 inline-block"></span>
            Your class day
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded border border-amber-400 bg-amber-50 inline-block"></span>
            No class (school break)
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded border border-gray-300 inline-block" style="background:repeating-linear-gradient(45deg,#e5e7eb,#e5e7eb 2px,#f3f4f6 2px,#f3f4f6 6px);"></span>
            School closed
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded border border-gray-200 bg-gray-50 opacity-50 inline-block"></span>
            Past day
        </div>
    </div>

    {{-- School year banner --}}
    @if($schoolYear)
    @php $isUpcoming = $schoolStart && $schoolStart->isFuture(); @endphp
    <div class="text-xs bg-gray-50 rounded-lg px-4 py-2 border border-gray-200 inline-flex items-center gap-2 {{ $isUpcoming ? 'border-amber-200 bg-amber-50' : '' }}">
        <svg class="w-3.5 h-3.5 {{ $isUpcoming ? 'text-amber-500' : 'text-navy/50' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        @if($isUpcoming)
            <span class="text-amber-700 font-medium">Upcoming:</span>
        @endif
        <strong class="text-navy">{{ $schoolYear->label }}</strong>
        &nbsp;·&nbsp;
        <span class="text-gray-500">{{ $schoolStart->format('d M Y') }} — {{ $schoolEnd->format('d M Y') }}</span>
    </div>
    @endif

    {{-- Month grids --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($months as $month)
        <div class="card !p-4">
            <h3 class="text-sm font-bold text-navy mb-3 uppercase tracking-wider">{{ $month['name'] }}</h3>

            <table style="width:100%; border-collapse:separate; border-spacing:2px;">
                <thead>
                    <tr>
                        @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $dow)
                        <th style="text-align:center; font-size:10px; font-weight:600; color:#9ca3af; padding-bottom:4px; width:14.28%;">{{ $dow }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($month['weeks'] as $week)
                    <tr>
                        @foreach($week as $cell)
                        <td style="padding:1px;">
                            @if($cell !== null)
                            @php
                                $hasClass    = count($cell['my_classes']) > 0;
                                $isOff       = $cell['is_off'];
                                $isPast      = $cell['is_past'];
                                $isToday     = $cell['is_today'];
                                $outsideYear = $cell['outside_year'];

                                if ($outsideYear) {
                                    // Outside school year — hatched grey
                                    $bg     = 'repeating-linear-gradient(45deg,#e5e7eb,#e5e7eb 2px,#f3f4f6 2px,#f3f4f6 6px)';
                                    $border = '1px solid #d1d5db';
                                    $color  = '#9ca3af';
                                    $extra  = '';
                                } elseif ($isOff && $hasClass) {
                                    // Off day that has one of my classes — amber + strikethrough
                                    $bg     = '#fef3c7';
                                    $border = '1.5px solid #f59e0b';
                                    $color  = '#92400e';
                                    $extra  = 'text-decoration:line-through;';
                                } elseif ($isOff) {
                                    // Off day, no class affected
                                    $bg     = '#fef9ec';
                                    $border = '1px solid #fcd34d';
                                    $color  = '#b45309';
                                    $extra  = '';
                                } elseif ($hasClass) {
                                    // My class day
                                    $bg     = '#eff6ff';
                                    $border = '1.5px solid #3569BF';
                                    $color  = '#1e3a8a';
                                    $extra  = 'font-weight:600;';
                                } elseif ($isToday) {
                                    $bg     = '#f0fdf4';
                                    $border = '1.5px solid #16a34a';
                                    $color  = '#15803d';
                                    $extra  = 'font-weight:700;';
                                } elseif ($isPast) {
                                    $bg     = '#f9fafb';
                                    $border = '1px solid #e5e7eb';
                                    $color  = '#d1d5db';
                                    $extra  = '';
                                } else {
                                    $bg     = '#fff';
                                    $border = '1px solid #e5e7eb';
                                    $color  = '#374151';
                                    $extra  = '';
                                }
                            @endphp
                            @php
                                $title = '';
                                if ($isOff && $cell['off_label']) $title .= $cell['off_label'];
                                if ($hasClass) {
                                    $names = collect($cell['my_classes'])->map(fn($c) => $c['class_name'] . ($c['dog_name'] ? ' (' . $c['dog_name'] . ')' : ''))->join(', ');
                                    $title .= ($title ? ' — ' : '') . $names;
                                }
                            @endphp
                            <div
                                title="{{ $title }}"
                                style="aspect-ratio:1/1; border-radius:4px; font-size:11px; {{ $extra }} cursor:default; display:flex; align-items:center; justify-content:center; user-select:none; background:{{ $bg }}; border:{{ $border }}; color:{{ $color }}; position:relative; background-size:8px 8px;">
                                {{ $cell['day'] }}
                                @if($isOff && $hasClass)
                                <span style="position:absolute;top:1px;right:2px;font-size:6px;color:#f59e0b;">●</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

    {{-- Upcoming no-class days summary --}}
    @if($upcomingOffDays->isNotEmpty())
    <div class="card border-l-4 border-amber-400">
        <h2 class="text-sm font-semibold text-navy mb-3">
            <svg class="w-4 h-4 inline-block mr-1 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            No-class days this year
        </h2>
        <div class="space-y-2">
            @foreach($upcomingOffDays as $off)
            <div class="flex items-start gap-3 py-2 border-b border-gray-100 last:border-0">
                <div class="text-center min-w-[48px]">
                    <div class="text-lg font-bold text-amber-600 leading-none">{{ $off['date']->format('d') }}</div>
                    <div class="text-xs text-gray-500 uppercase">{{ $off['date']->format('M') }}</div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-navy">{{ $off['label'] }}</p>
                    @if(count($off['classes']) > 0)
                        <p class="text-xs text-gray-500 mt-0.5">
                            Affected:
                            @foreach($off['classes'] as $cls)
                                <span class="font-medium">{{ $cls['class_name'] }}</span>
                                @if($cls['dog_name']) ({{ $cls['dog_name'] }})@endif{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </p>
                    @else
                        <p class="text-xs text-gray-400 mt-0.5">No classes of yours on this day</p>
                    @endif
                </div>
                <div class="text-xs text-gray-400 shrink-0">{{ $off['date']->format('l') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="card bg-green-50 border border-green-100">
        <div class="flex items-center gap-3 text-green-700">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">No cancelled class days in the next 12 months.</p>
        </div>
    </div>
    @endif

</div>
@endsection
