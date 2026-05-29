@php
$branch = \App\Models\BranchSetting::current();
$isLoggedIn = auth()->check();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enquire — {{ $classType->name }}</title>
    <link rel="icon" type="image/png" href="/icons/logo%20round.png">
    <link rel="apple-touch-icon" href="/icons/logo%20round.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { margin:0; padding:0; background:#e5e7eb; font-family:'Figtree','Nunito Sans','Helvetica Neue',Arial,sans-serif; }
        .page { max-width:560px; margin:0 auto; background:white; min-height:100vh; box-shadow:0 4px 24px rgba(0,0,0,0.12); }
        .header { background:#001d6d; padding:1.5rem; }
        .header h1 { color:white; font-size:1.3rem; font-weight:800; margin:0 0 0.25rem; }
        .header p { color:rgba(255,255,255,0.75); font-size:0.88rem; margin:0; }
        .body { padding:1.5rem; }
        .field { margin-bottom:1.25rem; }
        .field label { display:block; font-size:0.85rem; font-weight:600; color:#374151; margin-bottom:0.35rem; }
        .field input, .field textarea, .field select { width:100%; border:1px solid #d1d5db; border-radius:0.6rem; padding:0.6rem 0.75rem; font-size:0.9rem; background:#f9fafb; box-sizing:border-box; font-family:inherit; }
        .field textarea { resize:vertical; min-height:100px; }
        .field input:focus, .field textarea:focus { outline:none; border-color:#3964b0; box-shadow:0 0 0 2px rgba(57,100,176,0.15); }
        .field .hint { font-size:0.75rem; color:#9ca3af; margin-top:0.25rem; }
        .field .readonly { background:#f3f4f6; color:#6b7280; cursor:not-allowed; }
        .btn-primary { width:100%; background:#3964b0; color:white; font-weight:700; font-size:1rem; padding:0.75rem; border:none; border-radius:0.75rem; cursor:pointer; }
        .btn-primary:hover { background:#2d5099; }
        .class-card { background:#f0f4fb; border-radius:0.75rem; padding:0.85rem 1rem; margin-bottom:1.5rem; }
        .class-card p { margin:0; font-size:0.85rem; color:#374151; }
        .class-card .name { font-weight:700; font-size:0.95rem; color:#001d6d; margin-bottom:0.2rem; }
        .success { background:#d1fae5; border:1px solid #6ee7b7; border-radius:0.75rem; padding:1rem 1.25rem; color:#065f46; font-size:0.9rem; margin-bottom:1rem; }
        .error { background:#fee2e2; border:1px solid #fca5a5; border-radius:0.75rem; padding:1rem 1.25rem; color:#991b1b; font-size:0.85rem; margin-bottom:1rem; }
        .back { display:inline-flex; align-items:center; gap:0.4rem; font-size:0.82rem; color:rgba(255,255,255,0.7); text-decoration:none; margin-bottom:0.75rem; }
        .back:hover { color:white; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <a href="{{ route('class-info.show', $classType->slug) }}" class="back">← Back to {{ $classType->name }}</a>
        <h1>Enquire About This Class</h1>
        <p>{{ $classType->name }}{{ $specificClass ? ' · starting ' . $specificClass->start_date?->format('d M Y') : '' }}</p>
    </div>

    <div class="body">

        @if(session('success'))
        <div class="success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $e)<p style="margin:0 0 4px;">{{ $e }}</p>@endforeach
        </div>
        @endif

        @if($specificClass)
        <div class="class-card">
            <p class="name">{{ $classType->name }}</p>
            @if($specificClass->start_date)<p>Starts: {{ $specificClass->start_date->format('d M Y') }}</p>@endif
            @if($specificClass->start_time)<p>Time: {{ \Carbon\Carbon::parse($specificClass->start_time)->format('g:ia') }}@if($specificClass->end_time)–{{ \Carbon\Carbon::parse($specificClass->end_time)->format('g:ia') }}@endif</p>@endif
            @if($specificClass->location)<p>Location: {{ $specificClass->location }}</p>@endif
        </div>
        @endif

        <form method="POST" action="{{ route('class-info.enquire', $classType->slug) }}">
            @csrf
            @if($specificClass)
            <input type="hidden" name="class_id" value="{{ $specificClass->id }}">
            @endif

            @if($isLoggedIn)
                {{-- Pre-populated for logged-in users --}}
                @php $handler = auth()->user()->handler; $name = $handler ? $handler->first_name . ' ' . $handler->last_name : auth()->user()->name; @endphp
                <div class="field">
                    <label>Your Name</label>
                    <input type="text" value="{{ $name }}" class="readonly" readonly>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" value="{{ auth()->user()->email }}" class="readonly" readonly>
                </div>
                <div class="field">
                    <label>Message <span style="color:#6b7280;font-weight:400;">(optional)</span></label>
                    <textarea name="message" placeholder="Any questions or details you'd like us to know…">{{ old('message') }}</textarea>
                </div>
            @else
                {{-- Guest form --}}
                <div class="field">
                    <label>Your Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Jane Smith" required>
                </div>
                <div class="field">
                    <label>Email Address <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="jane@example.com" required>
                </div>
                <div class="field">
                    <label>Phone Number <span style="color:#6b7280;font-weight:400;">(optional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+27 82 000 0000">
                </div>
                <div class="field">
                    <label>Message <span style="color:#dc2626;">*</span></label>
                    <textarea name="message" placeholder="Tell us about your dog, any questions you have…" required>{{ old('message') }}</textarea>
                </div>
                <div class="field">
                    <label>How did you hear about us? <span style="color:#6b7280;font-weight:400;">(optional)</span></label>
                    <input type="text" name="heard_from" value="{{ old('heard_from') }}" placeholder="e.g. Google, Facebook, a friend…">
                </div>
            @endif

            <button type="submit" class="btn-primary">Send Enquiry →</button>
        </form>

        @if($branch->phone || $branch->email)
        <p style="font-size:0.8rem;color:#9ca3af;text-align:center;margin-top:1.25rem;">
            Prefer to call? Reach us at
            @if($branch->phone)<strong>{{ $branch->phone }}</strong>@endif
            @if($branch->phone && $branch->email) or @endif
            @if($branch->email)<a href="mailto:{{ $branch->email }}" style="color:#3964b0;">{{ $branch->email }}</a>@endif
        </p>
        @endif

    </div>
</div>
</body>
</html>
