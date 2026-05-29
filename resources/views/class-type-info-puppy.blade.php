@php
$p            = $classType->palette();
$overlayColor = $classType->hero_overlay_color ?: $p['primary'];
$overlayRgb   = implode(',', array_map('hexdec', str_split(ltrim($overlayColor, '#'), 2)));
$ink900       = '#404040';
$selectedClass = $selectedClass ?? null;
$branch       = \App\Models\BranchSetting::current();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $classType->name }} — {{ $branch->branch_name ?: 'McKaynine' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #e5e7eb; font-family: 'Figtree', 'Nunito Sans', 'Helvetica Neue', Arial, sans-serif; color: {{ $ink900 }}; }
        .page { max-width: 860px; margin: 0 auto; background: white; box-shadow: 0 4px 24px rgba(0,0,0,0.15); }

        .section-heading { font-size: 1.1rem; font-weight: 800; color: {{ $p['heading'] }}; margin: 0 0 0.6rem; line-height: 1.2; }
        .body-text { font-size: 0.85rem; line-height: 1.5; color: {{ $ink900 }}; margin: 0; }
        .check-li { display: flex; align-items: center; gap: 0.65rem; padding: 0.3rem 0; font-size: 0.9rem; font-weight: 600; }
        .check-circle { width: 1.5rem; height: 1.5rem; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .check-circle svg { width: 0.8rem; height: 0.8rem; }
        .step-badge { width: 2.25rem; height: 2.25rem; border-radius: 50%; background: {{ $p['step_badge'] }}; color: white; font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }

        .hero { position: relative; min-height: 320px; background: {{ $p['primary'] }}; overflow: hidden; display: flex; flex-direction: column; }
        .hero-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center top; }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, transparent 0%, transparent 30%, {{ $overlayColor }}88 55%, {{ $overlayColor }}ee 75%, {{ $overlayColor }} 100%); }
        .hero-overlay-plain { position: absolute; inset: 0; background: linear-gradient(135deg, {{ $overlayColor }} 60%, {{ $p['secondary'] }}); }
        .hero-text { position: relative; z-index: 10; flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: flex-end; padding: 2rem 2.5rem 2rem 50%; text-align: right; }
        .hero-text h1 { font-size: clamp(1.4rem, 3.5vw, 2.2rem); font-weight: 900; color: white; line-height: 1.15; letter-spacing: -0.01em; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); margin: 0; }
        .hero-text p { color: rgba(255,255,255,0.9); font-size: clamp(0.85rem, 2vw, 1rem); font-weight: 400; margin: 0.75rem 0 1.75rem; text-shadow: 1px 1px 4px rgba(0,0,0,0.4); padding-left: 3rem; }
        .hero-logo { height: 3.5rem; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3)); }
        .trust-strap { position: relative; z-index: 10; background: {{ $p['accent'] }}; color: white; text-align: center; padding: 0.5rem 1rem; }

        .two-col { display: grid; grid-template-columns: 40% 60%; }
        .left-col { background: {{ $p['left_col'] }}; padding: 1.5rem 1.25rem; display: flex; flex-direction: column; gap: 1.5rem; }
        .right-col { background: {{ $p['right_col'] }}; padding: 1.5rem 1.25rem; display: flex; flex-direction: column; gap: 1.25rem; }

        .gallery-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0; }
        .gallery-strip img { width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block; }

        .video-wrap { position: relative; width: 100%; aspect-ratio: 16/9; background: black; border-radius: 0.75rem; overflow: hidden; }
        .video-wrap iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }

        .footer { background: {{ $p['footer_bg'] }}; color: white; display: grid; grid-template-columns: auto 1fr auto auto; align-items: center; gap: 1.25rem; padding: 1rem 1.5rem; }
        .footer-logo { width: 4.5rem; height: 4.5rem; object-fit: contain; }
        .footer-bubble { background: {{ $p['btn_bg'] }}; color: {{ $p['btn_text'] }}; font-style: italic; font-size: 0.78rem; font-weight: 600; text-align: center; line-height: 1.4; padding: 0.6rem 1rem; border-radius: 40% 40% 40% 40% / 50% 50% 50% 50%; transform: rotate(-3deg); box-shadow: 0 2px 6px rgba(0,0,0,0.15); border: 2px solid white; }
        .footer-legal { font-size: 0.55rem; line-height: 1.5; color: rgba(255,255,255,0.85); max-width: 160px; }
        .footer-legal p { margin: 0 0 0.25rem; }

        /* Carousel */
        .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); background: white; border: none; border-radius: 50%; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2); z-index: 10; }
        .carousel-btn.prev { left: 0.5rem; }
        .carousel-btn.next { right: 0.5rem; }
        @@keyframes carouselIn  { from { opacity: 0; } to { opacity: 1; } }
        @@keyframes carouselOut { from { opacity: 1; } to { opacity: 0; } }

        @media (max-width: 640px) {
            .hero-overlay { background: linear-gradient(to bottom, rgba({{ $overlayRgb }},0.3) 0%, rgba({{ $overlayRgb }},0.75) 40%, rgba({{ $overlayRgb }},0.97) 100%); }
            .hero-text { padding: 1.75rem 1.25rem 1.25rem; align-items: center; text-align: center; }
            .hero-text h1 { font-size: 1.45rem; }
            .hero-text p { padding-left: 0; margin: 0.5rem 0 1.25rem; font-size: 0.9rem; }
            .hero-logo { height: 2.75rem; }
            .two-col { display: block; }
            .left-col, .right-col { padding: 1.25rem 1rem; }
            .footer { grid-template-columns: auto 1fr; grid-template-rows: auto auto; }
            .footer-logo { grid-row: 1; grid-column: 1; width: 3.5rem; height: 3.5rem; }
            .footer-contact { grid-row: 1; grid-column: 2; }
            .footer-bubble { display: none; }
            .footer-legal { grid-row: 2; grid-column: 1 / -1; max-width: none; font-size: 0.6rem; padding-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.2); }
        }
    </style>
</head>
<body>

@if(request()->boolean('preview') && auth()->check() && auth()->user()->is_admin)
<div style="background:#fbbf24;padding:0.4rem 1rem;text-align:center;font-size:0.78rem;font-weight:700;color:#78350f;">
    PREVIEW — not yet publicly visible{{ $selectedClass ? ' (individual class page)' : '' }}.
    <a href="{{ route('admin.class-types.edit', $classType) }}?_tab=info_page" style="margin-left:1rem;text-decoration:underline;">← Back to editor</a>
</div>
@endif

<div class="page">

{{-- 1. HERO --}}
<header class="hero">
    @if($classType->image_path)
    <picture>
        @if($classType->image_mobile_path)
        <source media="(max-width:640px)" srcset="{{ Storage::url($classType->image_mobile_path) }}">
        @endif
        <img src="{{ Storage::url($classType->image_path) }}" class="hero-img">
    </picture>
    <div class="hero-overlay"></div>
    @else
    <div class="hero-overlay-plain"></div>
    @endif

    <div class="hero-text">
        <h1>{{ $classType->hero_heading ?: 'Give your puppy the best start in life' }}</h1>
        @if($classType->tagline)<p>{{ $classType->tagline }}</p>@endif
        <img src="/icons/logo%20long.png" class="hero-logo">
    </div>

    @if($classType->trust_strap)
    <div class="trust-strap">
        @foreach(array_filter(array_map('trim', explode("\n", $classType->trust_strap))) as $line)
        <div style="font-size:0.82rem;{{ $loop->first ? 'font-weight:600;' : '' }}">{{ $line }}</div>
        @endforeach
    </div>
    @endif
</header>

{{-- 2. PROMO VIDEO (under banner, 2/3 width, centred) --}}
@if($classType->promo_video_url)
@php
    preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $classType->promo_video_url, $vm);
    $videoId = $vm[1] ?? '';
@endphp
@if($videoId)
<section style="background:{{ $p['right_col'] }};padding:1.5rem 1rem;">
    <div style="max-width:66%;margin:0 auto;">
        <div class="video-wrap">
            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</section>
@endif
@endif

{{-- 3. GALLERY CAROUSEL --}}
@if($classType->gallery_images && count($classType->gallery_images) > 0)
@php $galleryImages = array_values($classType->gallery_images); @endphp
<section style="background:{{ $p['primary'] }};padding:1rem 1rem;">
    <div x-data="{
        current: 0,
        prevCurrent: null,
        dir: 1,
        images: {{ json_encode($galleryImages) }},
        get count() { return this.images.length; },
        get prevIdx() { return (this.current - 1 + this.count) % this.count; },
        get nextIdx() { return (this.current + 1) % this.count; },
        prev() { this.dir = -1; this.prevCurrent = this.current; this.current = this.prevIdx; },
        next() { this.dir =  1; this.prevCurrent = this.current; this.current = this.nextIdx; }
    }" style="position:relative;padding:0 2.5rem;">
        <div style="display:flex;align-items:center;justify-content:center;gap:1rem;height:300px;overflow:hidden;">
            <div x-show="count > 1" style="flex-shrink:0;width:120px;height:300px;opacity:0.55;filter:blur(1.5px);overflow:hidden;">
                <div style="width:100%;height:100%;clip-path:polygon(0% 12.5%, 100% 0%, 100% 100%, 0% 87.5%);">
                    <img :src="`/storage/${images[prevIdx]}`" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
            </div>
            <div style="flex-shrink:0;width:300px;height:300px;position:relative;overflow:visible;">
                {{-- Outgoing image (fades out) --}}
                <img :src="prevCurrent !== null ? `/storage/${images[prevCurrent]}` : ''"
                     :style="`position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:0.75rem;border:3px solid white;box-shadow:0 2px 12px rgba(0,0,0,0.25);display:block;z-index:1;visibility:${prevCurrent!==null?'visible':'hidden'}`"
                     x-effect="if(prevCurrent===null)return; current; $el.style.animation='none'; void $el.offsetWidth; $el.style.animation='carouselOut 0.45s cubic-bezier(0.4,0,0.2,1) forwards';"
                     alt="">
                {{-- Incoming image (fades in) --}}
                <img :src="`/storage/${images[current]}`"
                     style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:0.75rem;border:3px solid white;box-shadow:0 2px 12px rgba(0,0,0,0.25);display:block;z-index:2;"
                     x-effect="current; $el.style.animation='none'; void $el.offsetWidth; $el.style.animation='carouselIn 0.45s cubic-bezier(0.4,0,0.2,1)';"
                     alt="">
            </div>
            <div x-show="count > 1" style="flex-shrink:0;width:120px;height:300px;opacity:0.55;filter:blur(1.5px);overflow:hidden;">
                <div style="width:100%;height:100%;clip-path:polygon(0% 0%, 100% 12.5%, 100% 87.5%, 0% 100%);">
                    <img :src="`/storage/${images[nextIdx]}`" alt="" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
            </div>
        </div>
        @if(count($galleryImages) > 1)
        <button @click="prev()" class="carousel-btn prev" type="button">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <button @click="next()" class="carousel-btn next" type="button">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M9 18l6-6-6-6"/></svg>
        </button>
        @endif
    </div>
</section>
@endif

{{-- 4. BODY --}}
<section class="two-col" id="class-details">

    {{-- LEFT --}}
    <div class="left-col">

        @if($classType->helps_with)
        <div>
            <p class="section-heading">What We Help With…</p>
            @foreach(array_filter(array_map('trim', explode("\n", $classType->helps_with))) as $item)
            <div class="check-li">
                <div class="check-circle">
                    <svg viewBox="0 0 20 20" fill="none" stroke="{{ $p['accent'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10l4 4 8-8"/>
                    </svg>
                </div>
                <span>{{ $item }}</span>
            </div>
            @endforeach
        </div>
        @endif

        @if($classType->age_requirements)
        <div>
            <p class="section-heading">When Can I Start?</p>
            @foreach(explode("\n\n", $classType->age_requirements) as $para)
            @if(trim($para))
            <p class="body-text" style="margin-bottom:0.5rem;">{{ trim($para) }}</p>
            @endif
            @endforeach
        </div>
        @endif

        @if($classType->testimonial_text)
        <div style="margin-top:auto;background:rgba(255,255,255,0.45);border-radius:0.5rem;padding:0.85rem;">
            @if($classType->testimonial_photo_path)
            <img src="{{ Storage::url($classType->testimonial_photo_path) }}"
                style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:0.375rem;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.15);display:block;margin-bottom:0.75rem;">
            @endif
            <p class="body-text" style="font-style:italic;line-height:1.6;">
                <span style="font-size:1.6rem;line-height:0;vertical-align:-0.35em;color:{{ $p['accent'] }};font-family:Georgia,serif;">&ldquo;</span>
                {{ $classType->testimonial_text }}
                <span style="font-size:1.6rem;line-height:0;vertical-align:-0.35em;color:{{ $p['accent'] }};font-family:Georgia,serif;">&rdquo;</span>
            </p>
            @if($classType->testimonial_name)
            <p style="font-size:0.78rem;font-style:italic;font-weight:600;color:{{ $p['heading'] }};margin-top:0.5rem;">— {{ $classType->testimonial_name }}</p>
            @endif
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div class="right-col">

        <div>
            <p class="section-heading">
                @if($selectedClass) This Class @else Where &amp; When… @endif
            </p>

            @if($selectedClass)
                @if($classType->general_schedule)
                <p class="body-text" style="margin-bottom:0.75rem;color:#6b7280;">{{ $classType->general_schedule }}</p>
                @endif

                {{-- White card: time, location, then month-grouped dates --}}
                <div style="background:white;border-radius:0.5rem;padding:0.75rem 1rem;display:flex;flex-direction:column;gap:0.5rem;margin-bottom:0.75rem;">
                    @if($selectedClass->start_time)
                    <p class="body-text"><strong>Time:</strong> {{ \Carbon\Carbon::parse($selectedClass->start_time)->format('g:ia') }}@if($selectedClass->end_time)–{{ \Carbon\Carbon::parse($selectedClass->end_time)->format('g:ia') }}@endif</p>
                    @endif
                    @if($selectedClass->location)
                    <p class="body-text"><strong>Location:</strong> {{ $selectedClass->location }}</p>
                    @endif

                    @if($selectedClass->scheduledDates->isNotEmpty())
                    @php
                        $byMonth = $selectedClass->scheduledDates->groupBy(fn($d) => $d->date->format('F Y'));
                    @endphp
                    <div style="border-top:1px solid #f0f0f0;margin-top:0.25rem;padding-top:0.5rem;display:flex;flex-direction:column;gap:0.6rem;">
                        @foreach($byMonth as $month => $dates)
                        <div>
                            <p style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:{{ $p['accent'] }};margin:0 0 0.2rem;">{{ $month }}</p>
                            <p class="body-text">{{ $dates->map(fn($d) => $d->date->format('D d'))->join(' · ') }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- CTA --}}
                @php
                    $ctaType = $classType->cta_type ?? 'enquire';
                    $classId = $selectedClass->id;
                    $isHandler = auth()->check() && auth()->user()?->handler;
                    $inlineEnrolUrl = $isHandler
                        ? route('enrol.choose-dog') . '?class_id=' . $classId
                        : route('enrol.start') . '?class_id=' . $classId;
                @endphp
                @if($ctaType === 'enquire')
                <a href="{{ route('class-info.enquire.form', $classType->slug) }}?class_id={{ $classId }}"
                    style="display:block;text-align:center;background:{{ $p['heading'] }};color:white;font-weight:700;font-size:0.9rem;padding:0.65rem 1rem;border-radius:0.5rem;text-decoration:none;border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                    Enquire Now →
                </a>
                @else
                <a href="{{ $inlineEnrolUrl }}"
                    style="display:block;text-align:center;background:{{ $p['heading'] }};color:white;font-weight:700;font-size:0.9rem;padding:0.65rem 1rem;border-radius:0.5rem;text-decoration:none;border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                    Enrol Now →
                </a>
                @endif

                <a href="{{ route('class-info.show', $classType->slug) }}"
                    style="display:block;font-size:0.78rem;color:{{ $p['accent'] }};text-decoration:underline;margin-top:0.5rem;">← View all available classes</a>

            @elseif($classType->individual_class_pages)
                @if($classType->general_schedule)
                <p class="body-text" style="margin-bottom:0.75rem;">{{ $classType->general_schedule }}</p>
                @endif
                @if($availableClasses->isNotEmpty())
                <p class="body-text" style="font-weight:700;margin-bottom:0.5rem;">Choose a class:</p>
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    @foreach($availableClasses as $c)
                    <div x-data="{ open: false }" style="background:white;border:1.5px solid {{ $p['left_col'] }};border-radius:0.5rem;overflow:hidden;">
                        <button x-on:click="open = !open" style="display:flex;justify-content:space-between;align-items:center;width:100%;padding:0.6rem 0.75rem;background:transparent;border:none;text-align:left;cursor:pointer;">
                            @php
                                $cTime = $c->start_time ? \Carbon\Carbon::parse($c->start_time)->format('g:ia') . ($c->end_time ? '–' . \Carbon\Carbon::parse($c->end_time)->format('g:ia') : '') : '';
                            @endphp
                            <div>
                                <p style="font-weight:700;font-size:0.88rem;margin:0;color:{{ $ink900 }};">{{ $c->name }}</p>
                                <p style="font-size:0.78rem;color:#6b7280;margin:0;">
                                    {{ $c->start_date?->format('d M Y') }}{{ $cTime ? ' · ' . $cTime : '' }}
                                </p>
                            </div>
                            <svg x-bind:style="open ? 'transform:rotate(180deg)' : ''" width="16" height="16" style="flex-shrink:0;color:#9ca3af;transition:transform 0.2s;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-collapse style="border-top:1px solid {{ $p['left_col'] }};padding:0.6rem 0.75rem;display:flex;flex-direction:column;gap:0.4rem;">
                            <p style="font-weight:700;font-size:0.78rem;color:#6b7280;margin:0;">Session dates:</p>
                            @php $byMonth = $c->scheduledDates->groupBy(fn($d) => $d->date->format('F Y')); @endphp
                            @foreach($byMonth as $month => $dates)
                            <p style="font-size:0.78rem;font-weight:700;color:{{ $p['heading'] }};margin:0.25rem 0 0.1rem;">{{ $month }}</p>
                            @foreach($dates as $d)
                            <p style="font-size:0.78rem;color:{{ $ink900 }};margin:0;">{{ $d->date->format('D j M') }}</p>
                            @endforeach
                            @endforeach
                            @php
                                $ctaTypeList = $classType->cta_type ?? 'enquire';
                                $isHandlerList = auth()->check() && auth()->user()?->handler;
                                $listEnrolUrl = $isHandlerList
                                    ? route('enrol.choose-dog') . '?class_id=' . $c->id
                                    : route('enrol.start') . '?class_id=' . $c->id;
                            @endphp
                            @if($ctaTypeList === 'enquire')
                            <a href="{{ route('class-info.enquire.form', $classType->slug) }}?class_id={{ $c->id }}"
                                style="display:inline-block;margin-top:0.5rem;background:{{ $p['btn_bg'] }};color:{{ $p['btn_text'] }};font-weight:700;font-size:0.82rem;padding:0.4rem 1rem;border-radius:2rem;text-decoration:none;">Enquire Now →</a>
                            @else
                            <a href="{{ $listEnrolUrl }}"
                                style="display:inline-block;margin-top:0.5rem;background:{{ $p['btn_bg'] }};color:{{ $p['btn_text'] }};font-weight:700;font-size:0.82rem;padding:0.4rem 1rem;border-radius:2rem;text-decoration:none;">Enrol Now →</a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="body-text" style="color:#6b7280;font-style:italic;">No classes currently available — check back soon.</p>
                @endif

            @else
                @if($classType->general_schedule)
                <p class="body-text" style="margin-bottom:0.75rem;">{{ $classType->general_schedule }}</p>
                @endif
                @if($availableClasses->isNotEmpty())
                <p class="body-text" style="font-weight:700;margin-bottom:0.4rem;">Upcoming available classes:</p>
                <ul style="list-style:disc;padding-left:1.1rem;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                    @foreach($availableClasses as $c)
                    <li class="body-text">
                        <strong>{{ $c->start_date?->format('d M Y') }}</strong>
                        @if($c->start_time) · {{ \Carbon\Carbon::parse($c->start_time)->format('g:ia') }}@if($c->end_time)–{{ \Carbon\Carbon::parse($c->end_time)->format('g:ia') }}@endif @endif
                        @if($c->location) · {{ $c->location }} @endif
                        @php $spots = $c->max_capacity - $c->confirmedEnrolments()->count(); @endphp
                        @if($spots <= 3) <em style="color:{{ $p['heading'] }};">({{ $spots }} spot{{ $spots === 1 ? '' : 's' }} left)</em> @endif
                    </li>
                    @endforeach
                </ul>
                @endif
            @endif
        </div>

        @php $feesImg = $selectedClass?->fees_image_path ?? $classType->fees_image_path; $feesImgMobile = $selectedClass?->fees_image_mobile_path ?? $classType->fees_image_mobile_path; @endphp
        <div style="display:flex;gap:1rem;align-items:flex-end;">
            <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:1rem;">
                <div>
                    <p class="section-heading">Course Fees</p>
                    <ul style="list-style:disc;padding-left:1.1rem;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                        @if($classType->cost_from)
                        <li class="body-text">Course fee: <strong>R{{ number_format($classType->cost_from, 2) }}</strong></li>
                        @endif
                        @if($classType->is_public && $branch->enrolment_fee)
                        <li class="body-text">Enrolment fee: <strong>R{{ number_format($branch->enrolment_fee, 2) }}</strong></li>
                        @endif
                        @if($classType->cost_notes)
                        <li class="body-text">{{ $classType->cost_notes }}</li>
                        @endif
                    </ul>
                </div>
                @if($classType->what_to_bring)
                <div>
                    <p class="section-heading">What Do I Need To Bring For Class?</p>
                    <ul style="list-style:disc;padding-left:1.1rem;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                        @foreach(array_filter(array_map('trim', explode("\n", $classType->what_to_bring))) as $item)
                        <li class="body-text">{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @if($feesImg)
            <div style="flex-shrink:0;width:40%;">
                <picture>
                    @if($feesImgMobile)
                    <source media="(max-width:640px)" srcset="{{ Storage::url($feesImgMobile) }}">
                    @endif
                    <img src="{{ Storage::url($feesImg) }}" style="width:100%;aspect-ratio:3/4;object-fit:cover;border-radius:0.5rem;display:block;box-shadow:0 2px 8px rgba(0,0,0,0.12);border:3px solid white;">
                </picture>
            </div>
            @endif
        </div>

        @if($classType->how_to_join_steps)
        @php
            $renderLinks = fn(string $text): string => preg_replace_callback(
                '/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/',
                fn($m) => '<a href="' . e($m[2]) . '" style="color:' . $blue700 . ';text-decoration:underline;font-weight:600;" target="_blank" rel="noopener">' . e($m[1]) . '</a>',
                e($text)
            );
        @endphp
        <div>
            <p class="section-heading">How To Join</p>
            <ol style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.75rem;">
                @foreach(array_filter(array_map('trim', explode("\n", $classType->how_to_join_steps))) as $step)
                <li style="display:flex;gap:0.75rem;">
                    <div class="step-badge">{{ $loop->iteration }}</div>
                    <p class="body-text" style="padding-top:0.35rem;">{!! $renderLinks($step) !!}</p>
                </li>
                @endforeach
            </ol>
            @if($classType->joining_notes)
            <ul style="list-style:disc;padding-left:1.1rem;margin:0.75rem 0 0;display:flex;flex-direction:column;gap:0.15rem;">
                @foreach(array_filter(array_map('trim', explode("\n", $classType->joining_notes))) as $note)
                <li style="font-size:0.78rem;color:{{ $ink900 }};line-height:1.4;">{!! $renderLinks($note) !!}</li>
                @endforeach
            </ul>
            @endif
        </div>
        @endif

        @if($branch->bank_account_number)
        <div>
            <p class="section-heading">Banking Details</p>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                @if($branch->bank_account_name)
                <li class="body-text"><strong>{{ $branch->bank_account_name }}</strong></li>
                @endif
                @if($branch->bank_name)
                <li class="body-text">Bank: {{ $branch->bank_name }}</li>
                @endif
                <li class="body-text">Acc: <strong>{{ $branch->bank_account_number }}</strong></li>
                @if($branch->bank_branch_code)
                <li class="body-text">Branch code: {{ $branch->bank_branch_code }}</li>
                @endif
                @if($branch->bank_reference_note)
                <li class="body-text" style="color:#6b7280;font-style:italic;">{{ $branch->bank_reference_note }}</li>
                @endif
            </ul>
        </div>
        @endif

    </div>
</section>

{{-- 5. CTA --}}
@php
$ctaType    = $classType->cta_type ?? 'enquire';
$classId    = $selectedClass?->id;
$isHandler  = auth()->check() && auth()->user()?->handler;
$footerEnrolUrl = $isHandler
    ? route('enrol.choose-dog') . ($classId ? '?class_id='.$classId : '')
    : route('enrol.start') . ($classId ? '?class_id='.$classId : '');
@endphp
<div style="background:{{ $p['primary'] }};padding:1.5rem 1.25rem;text-align:center;">
    <p style="color:white;font-weight:900;font-size:1.05rem;text-transform:uppercase;letter-spacing:0.02em;margin:0 0 0.5rem;line-height:1.4;border:2px solid rgba(255,255,255,0.4);display:inline-block;padding:0.4rem 1.25rem;border-radius:2rem;">
        We're looking forward to you and your pup in our classes!
    </p>
    <p style="color:rgba(255,255,255,0.85);font-size:0.88rem;margin:0.75rem 0 1rem;">
        @if($ctaType === 'enquire') Have questions? We'd love to hear from you.
        @else Ready to join? Select your dog to get started.
        @endif
    </p>

    @if($ctaType === 'enquire')
        <a href="{{ route('class-info.enquire.form', $classType->slug) }}{{ $classId ? '?class_id='.$classId : '' }}"
            style="display:inline-block;background:{{ $p['btn_bg'] }};color:{{ $p['btn_text'] }};font-weight:800;font-size:1rem;padding:0.65rem 2.5rem;border-radius:2rem;border:2px solid white;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
            Enquire Now →
        </a>
    @else
        <a href="{{ $footerEnrolUrl }}"
            style="display:inline-block;background:{{ $p['btn_bg'] }};color:{{ $p['btn_text'] }};font-weight:800;font-size:1rem;padding:0.65rem 2.5rem;border-radius:2rem;border:2px solid white;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
            Enrol Now →
        </a>
    @endif
</div>

{{-- 6. FOOTER --}}
<footer class="footer">
    <img src="/icons/logo%20round.png" class="footer-logo">

    <div class="footer-contact" style="display:flex;flex-direction:column;gap:0.3rem;">
        @if($branch->website)
        <div style="display:flex;align-items:center;gap:0.6rem;font-size:0.85rem;font-weight:600;">
            <span style="width:1.4rem;height:1.4rem;border-radius:50%;background:{{ $p['btn_bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="{{ $p['btn_text'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
            </span>
            {{ $branch->website }}
        </div>
        @endif
        @if($branch->email)
        <div style="display:flex;align-items:center;gap:0.6rem;font-size:0.82rem;">
            <span style="width:1.4rem;height:1.4rem;border-radius:50%;background:{{ $p['btn_bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="{{ $p['btn_text'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </span>
            {{ $branch->email }}
        </div>
        @endif
        @if($branch->phone)
        <div style="display:flex;align-items:center;gap:0.6rem;font-size:0.82rem;">
            <span style="width:1.4rem;height:1.4rem;border-radius:50%;background:{{ $p['btn_bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="{{ $p['btn_text'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
            </span>
            {{ $branch->phone }}
        </div>
        @endif
    </div>

    <div class="footer-bubble">
        We're looking forward<br>to you and your pup<br>in our classes!
    </div>

    <div class="footer-legal">
        @if($branch->legal_entity_name && $branch->branch_name)
        @php $franchisorReg = \App\Models\AppSetting::get('franchisor_registration_number'); @endphp
        <p>{{ $branch->legal_entity_name }} T/A {{ $branch->branch_name }}{{ $branch->legal_registration_number ? ' Reg. ' . $branch->legal_registration_number : '' }} is a licensed franchise of McKaynine Training pty ltd{{ $franchisorReg ? ' Reg. ' . $franchisorReg : '' }}</p>
        @endif
        @if($branch->legal_entity_name)
        <p>© {{ $branch->legal_entity_name }} — Unauthorised reproduction or modification is prohibited</p>
        @endif
    </div>
</footer>

</div>
</body>
</html>
