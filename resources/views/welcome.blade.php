<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $branch->branch_name ?? 'McKaynine Dog School' }}</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1A1D2E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── Typography ── */
        * { box-sizing: border-box; }
        body { font-family: 'Open Sans', sans-serif; margin: 0; background: #fff; }
        h1, h2, h3, .heading { font-family: 'Trebuchet MS', 'Open Sans', sans-serif; }

        /* ── Brand colours ── */
        :root {
            --navy:  #1A1D2E;
            --blue:  #3569BF;
            --gold:  #B8914F;
            --gold-light: #d4a860;
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { opacity: 0; animation: fadeUp 0.55s ease forwards; }
        .delay-1 { animation-delay: 0.05s; }
        .delay-2 { animation-delay: 0.18s; }

        /* ── Navbar ── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            padding: 0 1.5rem;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            background: var(--navy);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-logo img { height: 34px; width: auto; }
        .nav-signin {
            color: #fff;
            font-family: 'Trebuchet MS', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border: 1.5px solid rgba(255,255,255,0.45);
            border-radius: 999px;
            padding: 0.45rem 1.25rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-signin:hover { background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.8); }

        /* ── Cards section ── */
        .cards-section {
            background: var(--navy);
            padding: 80px 1.5rem 3.5rem; /* 80px = navbar height + breathing room */
        }
        .cards-grid {
            max-width: 860px; margin: 0 auto;
            display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;
        }
        @media (max-width: 640px) {
            .cards-section { padding-top: 88px; }
            .cards-grid { grid-template-columns: 1fr; }
        }

        /* Gold card */
        .feature-card {
            border-radius: 20px;
            padding: 1.75rem;
            display: flex; flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover { transform: translateY(-3px); }

        .card-gold {
            background: linear-gradient(140deg, #c9973e 0%, #B8914F 60%, #9e7338 100%);
            box-shadow: 0 8px 40px rgba(184,145,79,0.35);
        }
        .card-gold:hover { box-shadow: 0 14px 50px rgba(184,145,79,0.5); }

        .card-dark {
            background: linear-gradient(140deg, #252942 0%, #1e2238 100%);
            border: 1px solid rgba(255,255,255,0.07);
            box-shadow: 0 8px 40px rgba(0,0,0,0.3);
        }
        .card-dark:hover { box-shadow: 0 14px 50px rgba(0,0,0,0.45); }

        .card-eyebrow {
            font-size: 0.65rem; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            margin-bottom: 0.6rem;
        }
        .card-gold .card-eyebrow { color: rgba(255,255,255,0.65); }
        .card-dark .card-eyebrow { color: var(--gold-light); }

        .card-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem; flex-shrink: 0;
        }
        .card-gold .card-icon { background: rgba(255,255,255,0.18); }
        .card-dark .card-icon { background: rgba(184,145,79,0.15); }

        .card-heading {
            font-family: 'Trebuchet MS', 'Open Sans', sans-serif;
            font-size: 1.55rem; font-weight: 700;
            color: #fff; line-height: 1.15;
            margin: 0 0 0.5rem;
        }

        .card-rule {
            width: 36px; height: 2.5px; border-radius: 2px;
            margin-bottom: 0.9rem;
        }
        .card-gold .card-rule { background: rgba(255,255,255,0.4); }
        .card-dark .card-rule { background: var(--gold); }

        .card-desc {
            font-size: 0.85rem; line-height: 1.6;
            flex: 1; margin-bottom: 1.5rem;
        }
        .card-gold .card-desc { color: rgba(255,255,255,0.8); }
        .card-dark .card-desc { color: rgba(255,255,255,0.55); }

        .card-actions { display: flex; flex-wrap: wrap; gap: 0.65rem; }

        /* Buttons */
        .btn-pill {
            display: inline-flex; align-items: center; gap: 5px;
            font-family: 'Trebuchet MS', sans-serif;
            font-size: 0.75rem; font-weight: 700;
            letter-spacing: 0.06em; text-transform: uppercase;
            border-radius: 999px; padding: 0.6rem 1.4rem;
            text-decoration: none; transition: all 0.2s;
            white-space: nowrap;
        }
        /* Primary gold pill */
        .btn-pill-gold {
            background: var(--gold); color: #fff;
        }
        .btn-pill-gold:hover { background: var(--gold-light); transform: translateY(-1px); }

        /* Primary white pill */
        .btn-pill-white {
            background: rgba(255,255,255,0.95); color: var(--navy);
        }
        .btn-pill-white:hover { background: #fff; transform: translateY(-1px); }

        /* Outline white pill */
        .btn-pill-outline {
            background: transparent; color: rgba(255,255,255,0.85);
            border: 1.5px solid rgba(255,255,255,0.35);
        }
        .btn-pill-outline:hover { border-color: rgba(255,255,255,0.75); color: #fff; background: rgba(255,255,255,0.07); }

        /* Outline gold pill */
        .btn-pill-outline-gold {
            background: transparent; color: var(--gold-light);
            border: 1.5px solid rgba(184,145,79,0.5);
        }
        .btn-pill-outline-gold:hover { border-color: var(--gold); color: var(--gold); }

        /* ── Sign-in section ── */
        .signin-section {
            background: #fff;
            padding: 4rem 1.5rem;
            text-align: center;
        }
        .signin-inner { max-width: 420px; margin: 0 auto; }
        .signin-title {
            font-family: 'Trebuchet MS', 'Open Sans', sans-serif;
            font-size: 1.4rem; font-weight: 700;
            color: var(--navy); margin: 0 0 0.5rem;
        }
        .signin-sub {
            color: #9ca3af; font-size: 0.85rem;
            margin: 0 0 1.75rem; line-height: 1.5;
        }
        .btn-signin {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--navy); color: #fff;
            font-family: 'Trebuchet MS', sans-serif;
            font-size: 0.85rem; font-weight: 700;
            letter-spacing: 0.06em; text-transform: uppercase;
            border-radius: 999px; padding: 0.85rem 2.5rem;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-signin:hover { background: #2a3360; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,29,46,0.25); }
        .btn-signin svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* ── Footer ── */
        .site-footer {
            background: var(--navy);
            padding: 2.25rem 1.5rem;
            text-align: center;
        }
        .footer-brand {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 0.75rem;
        }
        .footer-brand img { height: 26px; width: auto; opacity: 0.7; }
        .footer-copy {
            color: rgba(255,255,255,0.3);
            font-size: 0.72rem; margin: 0 0 0.85rem;
        }
        .footer-back {
            display: inline-flex; align-items: center; gap: 6px;
            color: rgba(255,255,255,0.45);
            font-size: 0.78rem; font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-back:hover { color: var(--gold-light); }
        .footer-back svg { width: 13px; height: 13px; }

        /* ── Responsive tweaks ── */
        @media (max-width: 640px) {
            .card-heading { font-size: 1.35rem; }
            .signin-section { padding: 3rem 1.5rem; }
        }
    </style>
</head>
<body>

{{-- ════════════════════════════════════════ --}}
{{-- NAVBAR                                   --}}
{{-- ════════════════════════════════════════ --}}
<nav class="nav">
    <a href="/" class="nav-logo">
        <img src="{{ asset('icons/logo long.png') }}" alt="{{ $branch->branch_name ?? 'McKaynine Dog School' }}"
             onerror="this.outerHTML='<span style=\'color:#fff;font-family:Trebuchet MS;font-weight:700;font-size:1.1rem\'>McKaynine</span>'">
    </a>
    <a href="{{ route('login') }}" class="nav-signin">Sign In</a>
</nav>

{{-- ════════════════════════════════════════ --}}
{{-- FEATURE CARDS                            --}}
{{-- ════════════════════════════════════════ --}}
<div class="cards-section">
    <div class="cards-grid">

        {{-- ── PUPPY CARD ── --}}
        <div class="feature-card card-gold fade-up delay-1">
            <div class="card-icon">
                <svg width="22" height="22" fill="white" viewBox="0 0 200 200">
                    <ellipse cx="100" cy="130" rx="44" ry="36"/>
                    <ellipse cx="48"  cy="78"  rx="20" ry="26" transform="rotate(-20,48,78)"/>
                    <ellipse cx="80"  cy="58"  rx="20" ry="26" transform="rotate(-8,80,58)"/>
                    <ellipse cx="120" cy="58"  rx="20" ry="26" transform="rotate(8,120,58)"/>
                    <ellipse cx="152" cy="78"  rx="20" ry="26" transform="rotate(20,152,78)"/>
                </svg>
            </div>
            <p class="card-eyebrow">Puppies up to 4 months</p>
            <h2 class="card-heading">Puppy Class</h2>
            <div class="card-rule"></div>
            <p class="card-desc">
                Early socialisation, foundation skills, and a whole lot of fun. Get your pup off to the very best start in our structured puppy programme.
            </p>
            <div class="card-actions">
                <a href="{{ route('enrol.start') }}" class="btn-pill btn-pill-white">
                    Enrol Now
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                @if($puppyClass)
                <a href="{{ route('class-info.show', $puppyClass->slug) }}" class="btn-pill btn-pill-outline">
                    Find a Class
                </a>
                @endif
            </div>
        </div>

        {{-- ── GROUP CLASSES CARD ── --}}
        <div class="feature-card card-dark fade-up delay-2">
            <div class="card-icon">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" style="color: var(--gold-light);">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <p class="card-eyebrow">Pups older than 4 months</p>
            <h2 class="card-heading">Group Classes</h2>
            <div class="card-rule"></div>
            <p class="card-desc">
                Not sure which class is right for your dog? Book a short assessment and we'll place you in the perfect programme for your training goals.
            </p>
            <div class="card-actions">
                <a href="{{ route('enrol.assessment') }}" class="btn-pill btn-pill-gold">
                    Book an Assessment
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                @if($groupClasses->isNotEmpty())
                <a href="{{ route('class-info.show', $groupClasses->first()->slug) }}" class="btn-pill btn-pill-outline-gold">
                    Find a Class
                </a>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- SIGN IN                                  --}}
{{-- ════════════════════════════════════════ --}}
<section class="signin-section">
    <div class="signin-inner">
        <h2 class="signin-title">Already registered?</h2>
        <p class="signin-sub">Sign in to view your classes, upcoming sessions, messages, and training progress.</p>
        <a href="{{ route('login') }}" class="btn-signin">
            Sign In to My Account
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
        </a>
    </div>
</section>

{{-- ════════════════════════════════════════ --}}
{{-- FOOTER                                   --}}
{{-- ════════════════════════════════════════ --}}
<footer class="site-footer">
    <div class="footer-brand">
        <img src="{{ asset('icons/logo long.png') }}" alt="{{ $branch->branch_name ?? 'McKaynine' }}"
             onerror="this.style.display='none'">
    </div>
    <p class="footer-copy">
        © {{ date('Y') }} {{ $branch->branch_name ?? 'McKaynine Dog School' }}. All rights reserved.
    </p>
    <a href="https://www.mckaynine.co.za" target="_blank" rel="noopener" class="footer-back">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Back to McKaynine.co.za
    </a>
</footer>

</body>
</html>
