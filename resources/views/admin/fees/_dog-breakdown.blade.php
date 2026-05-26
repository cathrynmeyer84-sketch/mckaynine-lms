{{-- $line (has 'dogs', 'type', 'fee') --}}
@php $dogs = $line['dogs'] ?? []; @endphp
@if(count($dogs) > 0)
<div x-data="{ open: false }" class="mt-2">
    <button type="button" @click="open = !open"
        class="flex items-center gap-1 text-xs text-brand hover:text-navy font-medium transition-colors">
        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-90' : ''"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span x-text="open ? 'Hide dog breakdown' : 'Show dog breakdown ({{ count($dogs) }})'"></span>
    </button>
    <div x-show="open" x-transition class="mt-2">
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
            <tbody class="divide-y divide-gray-50">
                @foreach($dogs as $dog)
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
</div>
@endif
