@php
    $branch = \App\Models\BranchSetting::current();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'McKaynine') }}</title>
    <link rel="icon" type="image/png" href="/icons/logo%20round.png">
    <link rel="apple-touch-icon" href="/icons/logo%20round.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Open Sans', sans-serif;
            min-height: 100svh;
            display: flex;
            flex-direction: column;
        }

        /* ── Full-bleed background ── */
        .auth-bg {
            position: fixed; inset: 0; z-index: 0;
            background-size: cover;
            background-position: center 30%;
            background-repeat: no-repeat;
            @if($branch->hero_image_path)
            background-image: url('{{ Storage::url($branch->hero_image_path) }}');
            @else
            background: linear-gradient(160deg, #1A1D2E 0%, #2a3360 55%, #1A1D2E 100%);
            @endif
        }
        .auth-bg::after {
            content: '';
            position: absolute; inset: 0;
            background: rgba(26, 29, 46, 0.72);
        }

        /* Paw watermark */
        .auth-paw {
            position: fixed;
            right: 5%; bottom: 8%;
            z-index: 1;
            opacity: 0.05;
            width: 200px; height: 200px;
            pointer-events: none;
        }

        /* ── Centered wrapper ── */
        .auth-wrap {
            position: relative; z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            min-height: 100svh;
        }

        /* ── Logo above card ── */
        .auth-logo {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .auth-logo a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .auth-logo img {
            height: 38px;
            width: auto;
            opacity: 0.92;
        }

        /* ── Card ── */
        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 2.25rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35), 0 4px 16px rgba(0,0,0,0.2);
        }

        /* Gold top accent line */
        .auth-card::before {
            content: '';
            display: block;
            width: 40px; height: 3px;
            background: #B8914F;
            border-radius: 2px;
            margin: 0 auto 1.5rem;
        }

        /* ── Form elements override ── */
        .auth-card label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            letter-spacing: 0.02em;
        }
        .auth-card input[type="email"],
        .auth-card input[type="password"],
        .auth-card input[type="text"] {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
            font-family: 'Open Sans', sans-serif;
            color: #1A1D2E;
            background: #fafafa;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .auth-card input:focus {
            border-color: #3569BF;
            box-shadow: 0 0 0 3px rgba(53, 105, 191, 0.12);
            background: #fff;
        }

        /* ── Primary button ── */
        .auth-card button[type="submit"],
        .auth-card .btn-primary {
            width: 100%;
            background: #1A1D2E;
            color: #fff;
            font-family: 'Trebuchet MS', 'Open Sans', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            border: none;
            border-radius: 999px;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-top: 0.25rem;
        }
        .auth-card button[type="submit"]:hover {
            background: #2a3360;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,29,46,0.25);
        }

        /* ── Links inside card ── */
        .auth-card a {
            color: #3569BF;
            font-size: 0.8rem;
            text-decoration: none;
            transition: color 0.15s;
        }
        .auth-card a:hover { color: #1A1D2E; text-decoration: underline; }

        /* ── Remember me checkbox ── */
        .auth-card input[type="checkbox"] {
            width: auto;
            accent-color: #B8914F;
        }

        /* ── Back link below card ── */
        .auth-back {
            margin-top: 1.25rem;
            text-align: center;
        }
        .auth-back a {
            color: rgba(255,255,255,0.45);
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }
        .auth-back a:hover { color: #B8914F; }
        .auth-back svg { width: 13px; height: 13px; flex-shrink: 0; }

        /* ── Status / error messages ── */
        .auth-status {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 0.8rem;
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            margin-bottom: 1rem;
        }
        .auth-error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.3rem;
        }

        /* ── Spacing helpers ── */
        .auth-field { margin-bottom: 1rem; }
        .auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .auth-actions-stack {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

    {{-- Background --}}
    <div class="auth-bg"></div>

    {{-- Paw watermark --}}
    <svg class="auth-paw" viewBox="0 0 200 200" fill="white" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <ellipse cx="100" cy="130" rx="44" ry="36"/>
        <ellipse cx="48"  cy="78"  rx="20" ry="26" transform="rotate(-20,48,78)"/>
        <ellipse cx="80"  cy="58"  rx="20" ry="26" transform="rotate(-8,80,58)"/>
        <ellipse cx="120" cy="58"  rx="20" ry="26" transform="rotate(8,120,58)"/>
        <ellipse cx="152" cy="78"  rx="20" ry="26" transform="rotate(20,152,78)"/>
    </svg>

    <div class="auth-wrap">

        {{-- Logo --}}
        <div class="auth-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('icons/logo long.png') }}"
                     alt="{{ $branch->branch_name ?? 'McKaynine Dog School' }}"
                     onerror="this.outerHTML='<span style=\'color:#fff;font-family:Trebuchet MS;font-weight:700;font-size:1.2rem;letter-spacing:0.02em\'>McKaynine</span>'">
            </a>
        </div>

        {{-- Card --}}
        <div class="auth-card">
            {{ $slot }}
        </div>

        {{-- Back to home --}}
        <div class="auth-back">
            <a href="{{ url('/') }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to home
            </a>
        </div>

    </div>
</body>
</html>
