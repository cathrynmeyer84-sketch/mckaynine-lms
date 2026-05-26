@props(['blocks', 'class' => null])

@php
use Illuminate\Support\Str;

$dogClass = $class;
@endphp

<div class="message-body space-y-4">
@foreach($blocks as $block)
    @switch($block['type'])

        @case('text')
            <div class="text-sm text-gray-800 leading-relaxed prose prose-sm max-w-none prose-p:my-1 prose-strong:text-gray-900">{!! \Illuminate\Support\Str::markdown($block['content'] ?? '') !!}</div>
            @break

        @case('image')
            @if(!empty($block['path']))
            <div>
                <img src="{{ Storage::url($block['path']) }}" alt="{{ $block['caption'] ?? '' }}"
                    class="rounded-xl w-full max-w-lg object-cover">
                @if(!empty($block['caption']))
                <p class="text-xs text-gray-400 mt-1">{{ $block['caption'] }}</p>
                @endif
            </div>
            @endif
            @break

        @case('video')
            @if(!empty($block['youtube_url']))
            @php
                $videoId = '';
                preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $block['youtube_url'], $m);
                $videoId = $m[1] ?? '';
            @endphp
            @if($videoId)
            <div>
                @if(!empty($block['title']))
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ $block['title'] }}</p>
                @endif
                <div class="rounded-xl overflow-hidden aspect-video bg-black">
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                        class="w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            @endif
            @endif
            @break

        @case('checklist')
            <div class="bg-gray-50 rounded-xl p-4">
                @if(!empty($block['title']))
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">{{ $block['title'] }}</p>
                @endif
                <ul class="space-y-2">
                    @foreach($block['items'] ?? [] as $item)
                    <li class="flex items-start gap-2 text-sm text-gray-700">
                        <svg class="w-4 h-4 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @break

        @case('class_info')
            @if($dogClass)
            <div class="bg-brand/5 border border-brand/20 rounded-xl p-4 space-y-3">
                <p class="text-xs font-semibold text-brand uppercase tracking-wide">Class Details</p>
                @if($dogClass->start_time)
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @if($dogClass->start_date){{ $dogClass->start_date->format('l') }}s, @endif{{ \Carbon\Carbon::parse($dogClass->start_time)->format('g:ia') }}@if($dogClass->end_time) – {{ \Carbon\Carbon::parse($dogClass->end_time)->format('g:ia') }}@endif
                </div>
                @endif
                @php
                    $dates = $dogClass->scheduledDates ?? collect();
                    $byMonth = $dates->groupBy(fn($d) => $d->date->format('F'));
                @endphp
                @if($dates->count())
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <div class="space-y-1">
                        @foreach($byMonth as $month => $monthDates)
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ $month }}:</span>
                            {{ $monthDates->map(fn($d) => $d->date->format('j'))->join(', ') }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
            @break

        @case('next_class')
            @php
                $classIds     = $block['class_ids'] ?? [];
                $classTypeIds = $block['class_type_ids'] ?? [];

                $nextClasses = !empty($classIds)
                    ? \App\Models\DogClass::whereIn('id', $classIds)
                        ->with('classType')
                        ->get()
                    : collect();

                $nextClassTypes = !empty($classTypeIds)
                    ? \App\Models\ClassType::whereIn('id', $classTypeIds)
                        ->where('info_page_enabled', true)
                        ->whereNotNull('slug')
                        ->get()
                    : collect();

                $hasNext = $nextClasses->isNotEmpty() || $nextClassTypes->isNotEmpty();
            @endphp
            @if($hasNext)
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">What's Next?</p>

                @foreach($nextClassTypes as $ct)
                <a href="{{ url('/classes/' . $ct->slug) }}"
                    class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl hover:bg-brand/5 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $ct->name }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $ct->general_schedule ?: 'View available classes' }}
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach

                @foreach($nextClasses as $next)
                @if($next->info_slug && $next->info_page_enabled)
                <a href="{{ url('/classes/' . $next->info_slug) }}"
                    class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl hover:bg-brand/5 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $next->name }}</p>
                        @if($next->classType)<p class="text-xs text-gray-400">{{ $next->classType->name }}@if($next->start_date) · Starting {{ $next->start_date->format('d M Y') }}@endif</p>@endif
                    </div>
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @else
                <div class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $next->name }}</p>
                        @if($next->classType)<p class="text-xs text-gray-400">{{ $next->classType->name }}@if($next->start_date) · Starting {{ $next->start_date->format('d M Y') }}@endif</p>@endif
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
            @break

        @case('button')
            <div>
                <a href="{{ $block['url'] ?? '#' }}"
                    class="inline-block bg-brand text-white font-semibold text-sm px-6 py-3 rounded-xl hover:opacity-90 transition-opacity">
                    {{ $block['label'] ?? 'View' }}
                </a>
            </div>
            @break

        @case('divider')
            <hr class="border-gray-100">
            @break

        @case('class_content')
            {{-- Rendered from weekly content data passed into blocks at send time --}}
            @if(!empty($block['title']))
            <p class="font-semibold text-navy">{{ $block['title'] }}</p>
            @endif
            @if(!empty($block['description']))
            <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $block['description'] }}</div>
            @endif
            @if(!empty($block['youtube_url']))
            <x-message-blocks :blocks="[['type'=>'video','youtube_url'=>$block['youtube_url'],'title'=>'This Week\'s Video']]" />
            @endif
            @if(!empty($block['practice_items']))
            <x-message-blocks :blocks="[['type'=>'checklist','title'=>'Practice This Week','items'=>$block['practice_items']]]" />
            @endif
            @if(!empty($block['what_to_bring']))
            <div class="bg-amber/5 border border-amber/20 rounded-xl p-3">
                <p class="text-xs font-semibold text-amber uppercase tracking-wide mb-1">Bring Next Week</p>
                <p class="text-sm text-gray-700">{{ $block['what_to_bring'] }}</p>
            </div>
            @endif
            @break

    @endswitch
@endforeach
</div>
