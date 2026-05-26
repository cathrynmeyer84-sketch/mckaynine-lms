@php
// Defaults shown when fields are empty
$defaultHelps = ['Nipping','Chewing','Social Manners','Lead Walking','Confidence','Toilet Training'];
$defaultBring = [
    'Puppy wearing a normal flat buckle collar',
    'Light webbing lead (no chain or extendable leads please)',
    'LOTS of small, soft treats (treats on sale at classes)',
    'Towel or mat for your puppy to lie on',
    'Comfortable flat shoes and a hat in warm weather',
];
$defaultAgeReq  = "Puppies should start at 10 to 14 weeks old — we can sometimes make exceptions for small breeds on the upper age limit.\n\nPups MUST have had two vaccinations (latest from vet) before starting classes.";
$defaultJoining = "Cut-off for enrolment is one business day before your first lesson\nWe will send you a booking confirmation once we have received your docs";

$helpsWith    = (is_array($class->info_helps_with)    && count($class->info_helps_with))    ? $class->info_helps_with    : $defaultHelps;
$whatBring    = (is_array($class->info_what_to_bring) && count($class->info_what_to_bring)) ? $class->info_what_to_bring : $defaultBring;
$ageReq       = $class->info_age_requirements ?: $defaultAgeReq;
$joiningNotes = $class->info_joining_notes    ?: $defaultJoining;

$defaultFeeNotes = ['Includes Puppy Owner\'s Guide and a treat bag', '25% discount for simultaneous enrolment/s (excluding enrolment fee)'];
$feeNotes = (is_array($class->course_fee_notes) && count($class->course_fee_notes)) ? $class->course_fee_notes : $defaultFeeNotes;

// Brand colours
$forest900 = '#365236';
$forest700 = '#446C42';
$olive600  = '#647653';
$sage100   = '#C8DFD6';
$sage300   = '#9BC6B5';
$cream200  = '#D6C2B5';
$blue700   = '#3569BD';
$blue600   = '#4C7AC6';
$ink900    = '#404040';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $class->name }} — McKaynine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #e5e7eb; font-family: 'Figtree', 'Nunito Sans', 'Helvetica Neue', Arial, sans-serif; color: {{ $ink900 }}; }
        .page { max-width: 860px; margin: 0 auto; background: white; box-shadow: 0 4px 24px rgba(0,0,0,0.15); }

        .section-heading { font-size: 1.1rem; font-weight: 800; color: {{ $forest900 }}; margin: 0 0 0.6rem; line-height: 1.2; }
        .body-text { font-size: 0.85rem; line-height: 1.5; color: {{ $ink900 }}; margin: 0; }
        .bullet-dot { color: {{ $forest900 }}; font-weight: 900; flex-shrink: 0; margin-top: 1px; }
        .check-li { display: flex; align-items: center; gap: 0.65rem; padding: 0.3rem 0; font-size: 0.9rem; font-weight: 600; }
        .check-circle { width: 1.5rem; height: 1.5rem; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .check-circle svg { width: 0.8rem; height: 0.8rem; }
        .step-badge { width: 2.25rem; height: 2.25rem; border-radius: 50%; background: {{ $blue700 }}; color: white; font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }

        /* ── Hero ── */
        .hero { position: relative; min-height: 280px; background: {{ $forest900 }}; overflow: hidden; display: flex; flex-direction: column; }
        .hero-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; }
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to right, transparent 0%, transparent 30%, {{ $forest900 }}88 55%, {{ $forest900 }}ee 75%, {{ $forest900 }} 100%); }
        .hero-overlay-plain { position: absolute; inset: 0; background: linear-gradient(135deg, {{ $forest900 }} 60%, {{ $forest700 }}); }
        .hero-text { position: relative; z-index: 10; flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: flex-end; padding: 2rem 2.5rem 2rem 50%; text-align: right; }
        .hero-text h1 { font-size: clamp(1.4rem, 3.5vw, 2.2rem); font-weight: 900; color: white; line-height: 1.15; letter-spacing: -0.01em; text-shadow: 2px 2px 8px rgba(0,0,0,0.5); margin: 0; }
        .hero-text p { color: rgba(255,255,255,0.9); font-size: clamp(0.85rem, 2vw, 1rem); font-weight: 400; margin: 0.75rem 0 1.75rem; text-shadow: 1px 1px 4px rgba(0,0,0,0.4); padding-left: 3rem; }
        .hero-logo { height: 3.5rem; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3)); }
        .hero-classname { color: white; font-size: 1rem; font-weight: 700; margin: 0.6rem 0 0; text-shadow: 1px 1px 4px rgba(0,0,0,0.4); letter-spacing: 0.02em; }
        .trust-strap { position: relative; z-index: 10; background: {{ $olive600 }}; color: white; text-align: center; padding: 0.5rem 1rem; }

        /* ── Two-column body ── */
        .two-col { display: grid; grid-template-columns: 40% 60%; }
        .left-col { background: {{ $sage100 }}; padding: 1.5rem 1.25rem; display: flex; flex-direction: column; gap: 1.5rem; }
        .right-col { background: {{ $cream200 }}; padding: 1.5rem 1.25rem; display: flex; flex-direction: column; gap: 1.25rem; }

        /* ── Fees image wrapper ── */
        .fees-wrap { position: relative; }
        .fees-img { position: absolute; bottom: 0; right: 0; width: 9rem; height: 16rem; object-fit: cover; border-radius: 0.375rem; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); z-index: 10; }
        .fees-pad { padding-right: 10rem; }

        /* ── Footer ── */
        .footer { background: {{ $blue600 }}; color: white; display: grid; grid-template-columns: auto 1fr auto auto; align-items: center; gap: 1.25rem; padding: 1rem 1.5rem; }
        .footer-logo { width: 4.5rem; height: 4.5rem; object-fit: contain; }
        .footer-bubble { background: {{ $sage300 }}; color: {{ $forest900 }}; font-style: italic; font-size: 0.78rem; font-weight: 600; text-align: center; line-height: 1.4; padding: 0.6rem 1rem; border-radius: 40% 40% 40% 40% / 50% 50% 50% 50%; transform: rotate(-3deg); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .footer-legal { font-size: 0.55rem; line-height: 1.5; color: rgba(255,255,255,0.85); max-width: 160px; }
        .footer-legal p { margin: 0 0 0.25rem; }

        /* ── MOBILE ── */
        @media (max-width: 640px) {
            /* Hero: full-width overlay, text centred */
            .hero-overlay { background: linear-gradient(to bottom, rgba(54,82,54,0.3) 0%, rgba(54,82,54,0.75) 40%, rgba(54,82,54,0.97) 100%); }
            .hero-text { padding: 1.75rem 1.25rem 1.25rem; align-items: center; text-align: center; }
            .hero-text h1 { font-size: 1.45rem; }
            .hero-text p { padding-left: 0; margin: 0.5rem 0 1.25rem; font-size: 0.9rem; }
            .hero-logo { height: 2.75rem; }

            /* Body: stack columns */
            .two-col { display: block; }
            .left-col, .right-col { padding: 1.25rem 1rem; }

            /* Fees image: inline, not absolute */
            .fees-wrap { position: static; }
            .fees-img { position: static; width: 100%; height: auto; max-height: 13rem; object-fit: cover; margin-top: 1rem; display: block; }
            .fees-pad { padding-right: 0; }

            /* Footer: 2-column grid */
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
    PREVIEW — not yet publicly visible.
    <a href="{{ route('admin.classes.info-page', $class) }}" style="margin-left:1rem;text-decoration:underline;">← Back to editor</a>
</div>
@endif

<div class="page">

{{-- 1. HERO --}}
<header class="hero">
    @if($class->info_hero_image_path)
    <picture>
        @if($class->info_hero_image_mobile_path)
        <source media="(max-width:640px)" srcset="{{ Storage::url($class->info_hero_image_mobile_path) }}">
        @endif
        <img src="{{ Storage::url($class->info_hero_image_path) }}" class="hero-img">
    </picture>
    <div class="hero-overlay"></div>
    @else
    <div class="hero-overlay-plain"></div>
    @endif

    <div class="hero-text">
        <h1>Give your puppy the best start in life</h1>
        <p>Build calmness, confidence and great habits from day one</p>
        <img src="/icons/logo%20long.png" class="hero-logo">
        <p class="hero-classname">{{ $class->name }}</p>
    </div>

    <div class="trust-strap">
        <div style="font-size:0.82rem;font-weight:600;">Trusted by puppy owners since 1999</div>
        <div style="font-size:0.82rem;">Recommended by vets, breeders &amp; dog professionals</div>
    </div>
</header>

{{-- 2. BODY --}}
<section class="two-col">

    {{-- LEFT --}}
    <div class="left-col">

        <div>
            <p class="section-heading">What We Help With…</p>
            @foreach($helpsWith as $item)
            <div class="check-li">
                <div class="check-circle">
                    <svg viewBox="0 0 20 20" fill="none" stroke="{{ $olive600 }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10l4 4 8-8"/>
                    </svg>
                </div>
                <span>{{ $item }}</span>
            </div>
            @endforeach
        </div>

        <div>
            <p class="section-heading">When Can I Start?</p>
            @foreach(explode("\n\n", $ageReq) as $para)
            @if(trim($para))
            <p class="body-text" style="margin-bottom:0.5rem;">{{ trim($para) }}</p>
            @endif
            @endforeach
        </div>

        @if($class->testimonial_text)
        <div style="margin-top:auto;background:rgba(255,255,255,0.45);border-radius:0.5rem;padding:0.85rem;">
            @if($class->testimonial_photo_path)
            <img src="{{ Storage::url($class->testimonial_photo_path) }}"
                style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:0.375rem;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.15);display:block;margin-bottom:0.75rem;">
            @endif
            <p class="body-text" style="font-style:italic;line-height:1.6;">
                <span style="font-size:1.6rem;line-height:0;vertical-align:-0.35em;color:{{ $olive600 }};font-family:Georgia,serif;">&ldquo;</span> {{ $class->testimonial_text }}<span style="font-size:1.6rem;line-height:0;vertical-align:-0.35em;color:{{ $olive600 }};font-family:Georgia,serif;">&rdquo;</span>
            </p>
            @if($class->testimonial_name)
            <p style="font-size:0.78rem;font-style:italic;font-weight:600;color:{{ $forest900 }};margin-top:0.5rem;">— {{ $class->testimonial_name }}</p>
            @endif
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div class="right-col">

        {{-- Where & When + Course Fees with optional overlapping image --}}
        <div class="fees-wrap">

            @if($class->fees_image_path)
            <picture>
                @if($class->fees_image_mobile_path)
                <source media="(max-width:640px)" srcset="{{ Storage::url($class->fees_image_mobile_path) }}">
                @endif
                <img src="{{ Storage::url($class->fees_image_path) }}" class="fees-img">
            </picture>
            @endif

            <div class="{{ $class->fees_image_path ? 'fees-pad' : '' }}">
                <p class="section-heading">Where &amp; When…</p>
                <ul style="list-style:disc;padding-left:1.1rem;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                    @if($class->info_address)
                    <li class="body-text">{{ $class->info_address }}</li>
                    @endif
                    @if($class->scheduledDates->isNotEmpty())
                    <li class="body-text">
                        {{ $class->scheduledDates->first()->date->format('l') }}s
                        @if($class->start_time)
                            {{ \Carbon\Carbon::parse($class->start_time)->format('H\hi') }}@if($class->end_time) – {{ \Carbon\Carbon::parse($class->end_time)->format('H\hi') }}@endif
                        @endif
                    </li>
                    @php $datesByMonth = $class->scheduledDates->groupBy(fn($d) => $d->date->format('M Y')); @endphp
                    @foreach($datesByMonth as $monthLabel => $dates)
                    <li class="body-text">
                        <strong>{{ \Carbon\Carbon::parse($dates->first()->date)->format('M') }}:</strong>
                        @foreach($dates as $d){{ $d->date->format('j') }}@if(!$loop->last), @endif @endforeach
                    </li>
                    @endforeach
                    @endif
                </ul>
            </div>

            <div style="margin-top:1.25rem;" class="{{ $class->fees_image_path ? 'fees-pad' : '' }}">
                <p class="section-heading">Course Fees</p>
                <ul style="list-style:disc;padding-left:1.1rem;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                    @php $effectivePrice = $class->classType?->course_price ?? $class->course_price ?? null; @endphp
                    @if($effectivePrice)
                    <li class="body-text">
                        @if($class->scheduledDates->isNotEmpty()){{ $class->scheduledDates->count() }} Lesson Course: @endif
                        <strong>R{{ number_format($effectivePrice, 2) }}</strong> per puppy
                    </li>
                    @else
                    <li class="body-text"><strong>6 Lesson Course: R1 490.00</strong> per puppy</li>
                    @endif
                    @if($class->enrolment_fee)
                    <li class="body-text">Enrolment Fee: <strong>R{{ number_format($class->enrolment_fee, 2) }}</strong></li>
                    @else
                    <li class="body-text">Enrolment Fee: <strong>R265.00</strong></li>
                    @endif
                    @foreach($feeNotes as $note)
                    <li class="body-text">{{ $note }}</li>
                    @endforeach
                </ul>
            </div>

        </div>

        {{-- What to Bring --}}
        <div>
            <p class="section-heading">What Do I Need To Bring For Class?</p>
            <ul style="list-style:disc;padding-left:1.1rem;margin:0;display:flex;flex-direction:column;gap:0.2rem;">
                @foreach($whatBring as $item)
                <li class="body-text">{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Joining Details --}}
        <div>
            <p class="section-heading">Joining Details</p>
            <ol style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.75rem;">
                <li style="display:flex;gap:0.75rem;">
                    <div class="step-badge">1</div>
                    <p class="body-text" style="padding-top:0.35rem;">
                        Complete our enrolment form
                        @if($class->show_enrol_button)(<a href="{{ $enrolUrl }}" style="color:{{ $blue700 }};text-decoration:underline;">click here for the online form</a>)@endif
                        and drop us a copy of your pup's vaccination card.
                    </p>
                </li>
                <li style="display:flex;gap:0.75rem;">
                    <div class="step-badge">2</div>
                    <p class="body-text" style="padding-top:0.35rem;">
                        Submit the vet clearance letter <strong><u>only if</u></strong> your pup's last vacc was not from a vet (look for BVSc written after signature and practice stamp in vaccination book).
                    </p>
                </li>
                @if($class->bank_account_number)
                <li style="display:flex;gap:0.75rem;">
                    <div class="step-badge">3</div>
                    <div style="padding-top:0.35rem;">
                        <p class="body-text">Email us proof of payment to
                            @if($class->bank_account_name)<strong>{{ $class->bank_account_name }}</strong> — @endif
                            @if($class->bank_name){{ $class->bank_name }} @endif
                            @if($class->bank_account_number)Acc: <strong>{{ $class->bank_account_number }}</strong>@endif
                            @if($class->bank_branch_code). Branch Code: <strong>{{ $class->bank_branch_code }}</strong>@endif.
                            @if($class->bank_reference_note) {{ $class->bank_reference_note }}.@endif
                        </p>
                    </div>
                </li>
                @endif
            </ol>

            <ul style="list-style:disc;padding-left:1.1rem;margin:0.75rem 0 0;display:flex;flex-direction:column;gap:0.15rem;">
                @foreach(explode("\n", $joiningNotes) as $note)
                @if(trim($note))
                <li style="font-size:0.78rem;color:{{ $ink900 }};line-height:1.4;">{{ trim($note) }}</li>
                @endif
                @endforeach
            </ul>
        </div>

    </div>
</section>

{{-- 3. ENROL CTA --}}
@if($class->show_enrol_button)
<div style="background:{{ $forest900 }};padding:1.5rem 1.25rem;text-align:center;">
    <p style="color:white;font-weight:900;font-size:1.05rem;text-transform:uppercase;letter-spacing:0.02em;margin:0 0 0.5rem;line-height:1.4;">
        We're looking forward to you and your pup in our classes!
    </p>

    @if(($ctaMode ?? 'assessment') === 'enquiry')
        {{-- Enquiry only — no direct sign up --}}
        @if($class->contact_email || $class->contact_phone)
        <p style="color:rgba(255,255,255,0.85);font-size:0.9rem;margin:0.5rem 0 0;">
            To find out more, contact us:
            @if($class->contact_email) <a href="mailto:{{ $class->contact_email }}" style="color:{{ $sage300 }};font-weight:700;">{{ $class->contact_email }}</a> @endif
            @if($class->contact_phone) &nbsp;·&nbsp; <span style="color:{{ $sage300 }};font-weight:700;">{{ $class->contact_phone }}</span> @endif
        </p>
        @else
        <p style="color:rgba(255,255,255,0.85);font-size:0.9rem;margin:0.5rem 0 0;">Get in touch to find out more and reserve your spot.</p>
        @endif

    @elseif(($ctaMode ?? 'assessment') === 'direct')
        {{-- Direct sign-up --}}
        <a href="{{ $enrolUrl }}"
            style="display:inline-block;margin-top:0.75rem;background:{{ $sage300 }};color:{{ $forest900 }};font-weight:800;font-size:1rem;padding:0.65rem 2.5rem;border-radius:2rem;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
            Enrol Now →
        </a>

    @else
        {{-- Assessment required (default) --}}
        <p style="color:rgba(255,255,255,0.85);font-size:0.88rem;margin:0 0 0.75rem;">
            New students start with a free assessment session so we can match you to the right class.
        </p>
        <a href="{{ route('enrol.assessment') }}"
            style="display:inline-block;background:{{ $sage300 }};color:{{ $forest900 }};font-weight:800;font-size:1rem;padding:0.65rem 2.5rem;border-radius:2rem;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
            Book a Free Assessment →
        </a>
        @auth
        @if($handler)
        <p style="margin-top:0.75rem;">
            <a href="{{ $enrolUrl }}" style="color:{{ $sage300 }};font-size:0.85rem;text-decoration:underline;">
                Already assessed? Sign up directly →
            </a>
        </p>
        @endif
        @endauth
    @endif
</div>
@endif

{{-- 4. FOOTER --}}
<footer class="footer">
    <img src="/icons/logo%20round.png" class="footer-logo">

    <div class="footer-contact" style="display:flex;flex-direction:column;gap:0.4rem;">
        @if($class->contact_phone)
        <div style="display:flex;align-items:center;gap:0.6rem;font-size:0.9rem;font-weight:600;">
            <span style="width:1.6rem;height:1.6rem;border-radius:50%;background:{{ $sage300 }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $forest900 }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </span>
            {{ $class->contact_phone }}
        </div>
        @endif
        @if($class->contact_email)
        <div style="display:flex;align-items:center;gap:0.6rem;font-size:0.9rem;font-weight:600;">
            <span style="width:1.6rem;height:1.6rem;border-radius:50%;background:{{ $sage300 }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $forest900 }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </span>
            {{ $class->contact_email }}
        </div>
        @endif
        <div style="display:flex;align-items:center;gap:0.6rem;font-size:0.9rem;font-weight:600;">
            <span style="width:1.6rem;height:1.6rem;border-radius:50%;background:{{ $sage300 }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $forest900 }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
            </span>
            www.mckaynine.co.za
        </div>
    </div>

    <div class="footer-bubble">
        We're looking forward<br>to you and your pup<br>in our classes!
    </div>

    <div class="footer-legal">
        <p>McKaynine Kyalami is a licenced franchise of McKaynine (Pty) Ltd 2024/128375/07</p>
        <p>© McKaynine (Pty) Ltd — Info Pack. Unauthorised reproduction, distribution or modification is prohibited without prior written consent</p>
    </div>
</footer>

</div>
</body>
</html>
