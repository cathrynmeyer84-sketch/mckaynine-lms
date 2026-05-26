{{--
    Default class type info page.
    Template design to be supplied by client as PDF.
    Placeholder renders basic structured content in the meantime.
--}}
@php
$enquireUrl = route('class-info.enquire', $classType->slug);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $classType->name }} — McKaynine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

@if(request()->boolean('preview') && auth()->check() && auth()->user()->is_admin)
<div class="bg-amber-300 text-amber-900 text-xs font-bold text-center py-2 px-4">
    PREVIEW — not yet publicly visible.
    <a href="{{ route('admin.class-types.edit', $classType) . '?_tab=info_page' }}" class="ml-4 underline">← Back to editor</a>
</div>
@endif

<div class="max-w-3xl mx-auto bg-white shadow-xl min-h-screen">

    {{-- Hero --}}
    <div class="relative min-h-56 bg-brand overflow-hidden flex items-end">
        @if($classType->image_path)
        <picture>
            @if($classType->image_mobile_path)
            <source media="(max-width:640px)" srcset="{{ Storage::url($classType->image_mobile_path) }}">
            @endif
            <img src="{{ Storage::url($classType->image_path) }}" class="absolute inset-0 w-full h-full object-cover opacity-40">
        </picture>
        @endif
        <div class="relative z-10 p-8">
            <img src="/icons/logo%20long.png" class="h-10 mb-4 filter brightness-0 invert">
            <h1 class="text-3xl font-black text-white leading-tight">{{ $classType->name }}</h1>
            @if($classType->tagline)
            <p class="text-white/80 mt-2 text-base">{{ $classType->tagline }}</p>
            @endif
        </div>
    </div>

    <div class="p-8 space-y-10">

        {{-- About --}}
        @if($classType->about)
        <section>
            <h2 class="text-xl font-black text-navy mb-3">About This Class</h2>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $classType->about }}</p>
        </section>
        @endif

        {{-- Video --}}
        @if($classType->promo_video_url)
        @php
            preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $classType->promo_video_url, $vm);
            $videoId = $vm[1] ?? '';
        @endphp
        @if($videoId)
        <section>
            <div class="rounded-[40px] overflow-hidden aspect-video bg-black">
                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" class="w-full h-full" frameborder="0" allowfullscreen loading="lazy"></iframe>
            </div>
        </section>
        @endif
        @endif

        {{-- Schedule & Cost --}}
        @if($classType->general_schedule || $availableClasses->isNotEmpty() || $classType->cost_from)
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($classType->general_schedule || $availableClasses->isNotEmpty())
            <div class="bg-brand/5 border border-brand/10 rounded-2xl p-5">
                <h2 class="text-base font-black text-navy mb-2">When & Where</h2>
                @if($classType->general_schedule)
                <p class="text-sm text-gray-700 mb-3">{{ $classType->general_schedule }}</p>
                @endif
                @if($availableClasses->isNotEmpty())
                <ul class="space-y-1">
                    @foreach($availableClasses as $c)
                    <li class="text-sm text-gray-700">
                        <strong>{{ $c->start_date?->format('d M Y') }}</strong>
                        @if($c->start_time) · {{ \Carbon\Carbon::parse($c->start_time)->format('g:ia') }} @endif
                        @if($c->location) · {{ $c->location }} @endif
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
            @endif
            @if($classType->cost_from)
            <div class="bg-amber/5 border border-amber/10 rounded-2xl p-5">
                <h2 class="text-base font-black text-navy mb-2">Fees</h2>
                <p class="text-2xl font-black text-brand">From ${{ number_format($classType->cost_from, 2) }}</p>
                @if($classType->cost_notes)
                <p class="text-sm text-gray-600 mt-1">{{ $classType->cost_notes }}</p>
                @endif
            </div>
            @endif
        </section>
        @endif

        {{-- Gallery --}}
        @if($classType->gallery_images && count($classType->gallery_images))
        <section class="grid grid-cols-3 gap-2">
            @foreach($classType->gallery_images as $img)
            <img src="{{ Storage::url($img) }}" class="w-full aspect-square object-cover rounded-2xl ring-4 ring-brand/20">
            @endforeach
        </section>
        @endif

        {{-- Testimonial --}}
        @if($classType->testimonial_text)
        <section class="bg-brand/5 border border-brand/10 rounded-2xl p-6 flex gap-4 items-start">
            @if($classType->testimonial_photo_path)
            <img src="{{ Storage::url($classType->testimonial_photo_path) }}" class="w-16 h-16 object-cover rounded-full ring-4 ring-brand/20 shrink-0">
            @endif
            <div>
                <p class="italic text-gray-700 leading-relaxed">&ldquo;{{ $classType->testimonial_text }}&rdquo;</p>
                @if($classType->testimonial_name)
                <p class="text-sm font-semibold text-gray-400 mt-2">— {{ $classType->testimonial_name }}</p>
                @endif
            </div>
        </section>
        @endif

        {{-- CTA --}}
        <section class="text-center py-6 border-t border-gray-100">
            <form method="POST" action="{{ $enquireUrl }}">
                @csrf
                <button type="submit" class="bg-brand text-white font-black text-lg px-10 py-4 rounded-full shadow-lg hover:opacity-90 transition-opacity">
                    @auth Enquire Now → @else Find Out More → @endauth
                </button>
            </form>
            @auth
            <p class="text-sm text-gray-400 mt-3">We'll be in touch to confirm your place.</p>
            @else
            <p class="text-sm text-gray-400 mt-3">We'll match you to the right class and get in touch.</p>
            @endauth
        </section>

    </div>
</div>

</body>
</html>
