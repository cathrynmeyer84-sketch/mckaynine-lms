@php
    $branch = \App\Models\BranchSetting::current();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation Invalid — {{ $branch->branch_name ?: 'McKaynine' }}</title>
    <link rel="icon" type="image/png" href="/icons/logo%20round.png">
    <link rel="apple-touch-icon" href="/icons/logo%20round.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { margin:0; font-family:'Open Sans',sans-serif; background:#f3f4f6; min-height:100svh; display:flex; align-items:center; justify-content:center; }
        .card { background:#fff; border-radius:16px; padding:2.5rem 2rem; max-width:400px; width:100%; text-align:center; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
        h1 { margin:0 0 0.5rem; font-size:1.15rem; color:#1A1D2E; }
        p { margin:0 0 1.25rem; font-size:0.88rem; color:#6b7280; line-height:1.6; }
        a { display:inline-block; padding:0.65rem 1.5rem; background:#1A1D2E; color:#fff; text-decoration:none; border-radius:999px; font-size:0.82rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; }
    </style>
</head>
<body>
<div class="card">
    <svg style="width:48px;height:48px;color:#d1d5db;margin:0 auto 1rem" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    @if($reason === 'already used')
        <h1>Account already created</h1>
        <p>This invitation link has already been used to create an account. Please log in instead.</p>
        <a href="{{ route('login') }}">Log In</a>
    @else
        <h1>Invitation expired</h1>
        <p>This invitation link has expired. Please contact {{ $branch->branch_name ?: 'the school' }} to request a new one.</p>
    @endif
</div>
</body>
</html>
