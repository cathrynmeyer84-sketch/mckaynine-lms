<x-app-layout :title="$resource->title">
<div class="page-content">

    {{-- Back button (top) --}}
    <div class="mb-5">
        <button
            onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('handler.resources.index') }}'"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-navy transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Resources
        </button>
    </div>

    <div class="max-w-2xl mx-auto">

        {{-- Hero image --}}
        @if($resource->image_path)
        <div class="rounded-2xl overflow-hidden mb-6 bg-gray-100">
            <img
                src="{{ Storage::url($resource->image_path) }}"
                alt="{{ $resource->title }}"
                class="w-full object-cover max-h-72"
            >
        </div>
        @endif

        {{-- Article card --}}
        <div class="card">

            {{-- Category + title --}}
            @if($resource->category)
            <p class="text-xs font-semibold text-brand uppercase tracking-wide mb-1">{{ $resource->category }}</p>
            @endif
            <h1 class="text-xl font-bold text-navy leading-snug mb-5">{{ $resource->title }}</h1>

            {{-- Content --}}
            @if($resource->content)
            <div class="prose prose-sm max-w-none text-gray-700">
                {!! Illuminate\Support\Str::markdown($resource->content) !!}
            </div>
            @endif

            {{-- External link --}}
            @if($resource->external_url)
            <div class="mt-6 pt-5 border-t border-gray-100">
                <a href="{{ $resource->external_url }}" target="_blank" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Open link
                </a>
            </div>
            @endif

            {{-- Downloadable file --}}
            @if($resource->file_path)
            <div class="mt-6 pt-5 border-t border-gray-100">
                <a href="{{ Storage::url($resource->file_path) }}" target="_blank" class="btn-outline inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download file
                </a>
            </div>
            @endif

        </div>

        {{-- Back button (bottom) --}}
        <div class="mt-6">
            <button
                onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('handler.resources.index') }}'"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-navy transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Resources
            </button>
        </div>

    </div>
</div>
</x-app-layout>
