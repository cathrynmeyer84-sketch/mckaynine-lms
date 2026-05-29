@php
    $branch = \App\Models\BranchSetting::current();
    $nameParts = explode(' ', $invitation->name ?? '', 2);
    $defaultFirst = $nameParts[0] ?? '';
    $defaultLast  = $nameParts[1] ?? '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Instructor Account Setup — {{ $branch->branch_name ?: 'McKaynine Dog School' }}</title>
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
            background: #f3f4f6;
            min-height: 100svh;
            color: #1A1D2E;
        }

        .page-wrap {
            max-width: 520px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 4rem;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-header img { height: 36px; width: auto; }
        .brand-header h1 { margin: 0.75rem 0 0.25rem; font-size: 1.35rem; font-weight: 700; color: #1A1D2E; }
        .brand-header p { margin: 0; font-size: 0.85rem; color: #6b7280; }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            margin-bottom: 1.25rem;
        }
        .card-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #B8914F;
            margin: 0 0 1.25rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .card-title svg { width: 14px; height: 14px; }

        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            letter-spacing: 0.02em;
        }
        .field input, .field textarea {
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
        .field textarea { resize: vertical; min-height: 90px; }
        .field input:focus, .field textarea:focus {
            border-color: #3569BF;
            box-shadow: 0 0 0 3px rgba(53,105,191,0.1);
            background: #fff;
        }
        .field input.has-error { border-color: #dc2626; }
        .error { color: #dc2626; font-size: 0.75rem; margin-top: 0.3rem; }
        .hint { font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        @media (max-width: 480px) { .row-2 { grid-template-columns: 1fr; } }

        /* Photo upload */
        .photo-upload-row { display: flex; align-items: center; gap: 1rem; }
        .photo-preview {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: #e5e7eb;
            overflow: hidden;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .photo-preview img { width: 100%; height: 100%; object-fit: cover; }
        .photo-preview svg { width: 28px; height: 28px; color: #9ca3af; }
        .photo-pick {
            flex: 1;
            border: 1.5px dashed #d1d5db;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            cursor: pointer;
            font-size: 0.82rem;
            color: #6b7280;
            font-weight: 600;
            display: flex; align-items: center; gap: 8px;
            transition: border-color 0.2s, background 0.2s;
            background: #fafafa;
        }
        .photo-pick:hover { border-color: #3569BF; color: #3569BF; background: rgba(53,105,191,0.03); }
        .photo-pick svg { width: 18px; height: 18px; flex-shrink: 0; }

        .submit-btn {
            width: 100%;
            background: #1A1D2E;
            color: #fff;
            font-family: 'Open Sans', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border: none;
            border-radius: 999px;
            padding: 0.9rem 1.5rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            margin-top: 0.5rem;
        }
        .submit-btn:hover {
            background: #2a3360;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,29,46,0.22);
        }

        .char-count { font-size: 0.72rem; color: #9ca3af; text-align: right; margin-top: 0.2rem; }
    </style>
</head>
<body>
<div class="page-wrap"
     x-data="{
        photoPreview: null,
        bioLength: 0,
        setPhoto(e) {
            const f = e.target.files[0];
            if (!f) return;
            const reader = new FileReader();
            reader.onload = ev => this.photoPreview = ev.target.result;
            reader.readAsDataURL(f);
        }
     }">

    {{-- Header --}}
    <div class="brand-header">
        <img src="{{ asset('icons/logo long.png') }}"
             alt="{{ $branch->branch_name ?: 'McKaynine Dog School' }}"
             onerror="this.outerHTML='<strong style=\'font-size:1.1rem;color:#1A1D2E\'>McKaynine</strong>'">
        <h1>Instructor Account Setup</h1>
        <p>{{ $branch->branch_name ?: 'McKaynine Dog School' }}</p>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#dc2626;border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.82rem;">
        <strong>Please fix the following:</strong>
        <ul style="margin:0.4rem 0 0 1.2rem;padding:0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('invitation.instructor.store', $invitation->token) }}"
          enctype="multipart/form-data">
        @csrf

        {{-- ── Your Details ── --}}
        <div class="card">
            <p class="card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Your Details
            </p>

            <div class="row-2">
                <div class="field">
                    <label>First name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="first_name" required
                           value="{{ old('first_name', $defaultFirst) }}"
                           class="{{ $errors->has('first_name') ? 'has-error' : '' }}">
                    @error('first_name')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Last name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="last_name" required
                           value="{{ old('last_name', $defaultLast) }}"
                           class="{{ $errors->has('last_name') ? 'has-error' : '' }}">
                    @error('last_name')<p class="error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="field">
                <label>Email address <span style="color:#dc2626">*</span></label>
                <input type="email" name="email" required
                       value="{{ old('email', $invitation->email) }}"
                       class="{{ $errors->has('email') ? 'has-error' : '' }}">
                @error('email')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label>Phone number</label>
                <input type="tel" name="phone"
                       value="{{ old('phone') }}"
                       placeholder="e.g. 082 123 4567">
            </div>

            <div class="field">
                <label>Date of birth</label>
                <input type="date" name="birthday"
                       value="{{ old('birthday') }}"
                       max="{{ date('Y-m-d') }}">
                <p class="hint">Used internally — not shown to students.</p>
            </div>
        </div>

        {{-- ── Password ── --}}
        <div class="card">
            <p class="card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Set Your Password
            </p>

            <div class="row-2">
                <div class="field">
                    <label>Password <span style="color:#dc2626">*</span></label>
                    <input type="password" name="password" required
                           autocomplete="new-password"
                           placeholder="At least 8 characters"
                           class="{{ $errors->has('password') ? 'has-error' : '' }}">
                    @error('password')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Confirm password <span style="color:#dc2626">*</span></label>
                    <input type="password" name="password_confirmation" required
                           autocomplete="new-password"
                           placeholder="Repeat password">
                </div>
            </div>
        </div>

        {{-- ── Profile ── --}}
        <div class="card">
            <p class="card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Your Profile
            </p>

            {{-- Photo --}}
            <div class="field">
                <label>Profile photo <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
                <div class="photo-upload-row">
                    <div class="photo-preview">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" alt="Preview">
                        </template>
                        <template x-if="!photoPreview">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </template>
                    </div>
                    <label class="photo-pick" @click="$refs.photoInput.click()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-text="photoPreview ? 'Change photo' : 'Upload a photo'"></span>
                    </label>
                    <input type="file" name="photo" accept="image/*"
                           x-ref="photoInput" class="hidden"
                           @change="setPhoto($event)">
                </div>
                @error('photo')<p class="error">{{ $message }}</p>@enderror
            </div>

            {{-- Bio --}}
            <div class="field" style="margin-top:1rem">
                <label>Short bio <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
                <textarea name="bio" rows="4"
                          maxlength="1000"
                          placeholder="A few sentences about your background, training philosophy, or what you enjoy most about teaching..."
                          @input="bioLength = $event.target.value.length"
                          class="{{ $errors->has('bio') ? 'has-error' : '' }}">{{ old('bio') }}</textarea>
                <p class="char-count"><span x-text="bioLength"></span>/1000</p>
                <p class="hint">This will appear on your public profile. You can always update it later.</p>
                @error('bio')<p class="error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="submit-btn">Create My Account →</button>

    </form>
</div>
</body>
</html>
