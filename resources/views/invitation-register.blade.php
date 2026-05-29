@php
    $branch = \App\Models\BranchSetting::current();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Your Account — {{ $branch->branch_name ?: 'McKaynine Dog School' }}</title>
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
            max-width: 560px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 4rem;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-header img {
            height: 36px;
            width: auto;
        }
        .brand-header h1 {
            margin: 0.75rem 0 0.25rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: #1A1D2E;
        }
        .brand-header p {
            margin: 0;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            margin-bottom: 1.25rem;
        }
        .card-title {
            font-size: 0.8rem;
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
        .field input, .field select {
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
        .field input:focus, .field select:focus {
            border-color: #3569BF;
            box-shadow: 0 0 0 3px rgba(53,105,191,0.1);
            background: #fff;
        }
        .field input.has-error { border-color: #dc2626; }
        .error { color: #dc2626; font-size: 0.75rem; margin-top: 0.3rem; }

        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        @media (max-width: 480px) { .row-2 { grid-template-columns: 1fr; } }

        /* Dog card */
        .dog-card {
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 0.75rem;
            background: #fafafa;
            position: relative;
        }
        .dog-card-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dog-remove {
            background: none;
            border: none;
            cursor: pointer;
            color: #dc2626;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: background 0.15s;
        }
        .dog-remove:hover { background: #fef2f2; }

        .add-dog-btn {
            width: 100%;
            border: 1.5px dashed #d1d5db;
            border-radius: 10px;
            background: none;
            padding: 0.65rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
            margin-top: 0.5rem;
        }
        .add-dog-btn:hover {
            border-color: #3569BF;
            color: #3569BF;
            background: rgba(53,105,191,0.04);
        }

        /* Photo upload */
        .photo-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1.5px dashed #d1d5db;
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            background: #fff;
            text-align: center;
        }
        .photo-label:hover { border-color: #3569BF; background: rgba(53,105,191,0.03); }
        .photo-label svg { width: 24px; height: 24px; color: #9ca3af; }
        .photo-label span { font-size: 0.75rem; color: #9ca3af; }
        .photo-label strong { font-size: 0.78rem; color: #374151; }

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

        .hint { font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; }
    </style>
</head>
<body>
<div class="page-wrap"
     x-data="{
        dogs: [{ name: '', breed: '', date_of_birth: '', gender: '', photoName: '' }],
        addDog() { this.dogs.push({ name: '', breed: '', date_of_birth: '', gender: '', photoName: '' }); },
        removeDog(i) { if (this.dogs.length > 1) this.dogs.splice(i, 1); },
        setPhoto(i, e) { const f = e.target.files[0]; this.dogs[i].photoName = f ? f.name : ''; }
     }">

    {{-- Header --}}
    <div class="brand-header">
        <img src="{{ asset('icons/logo long.png') }}"
             alt="{{ $branch->branch_name ?: 'McKaynine Dog School' }}"
             onerror="this.outerHTML='<strong style=\'font-size:1.1rem;color:#1A1D2E\'>McKaynine</strong>'">
        <h1>Create Your Account</h1>
        <p>You've been invited by {{ $branch->branch_name ?: 'McKaynine Dog School' }}</p>
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

    <form method="POST" action="{{ route('invitation.register.store', $invitation->token) }}"
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
                           value="{{ old('first_name', $invitation->name) }}"
                           placeholder="Sarah"
                           class="{{ $errors->has('first_name') ? 'has-error' : '' }}">
                    @error('first_name')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="field">
                    <label>Last name <span style="color:#dc2626">*</span></label>
                    <input type="text" name="last_name" required
                           value="{{ old('last_name') }}"
                           placeholder="Smith"
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
                <label>Cell number</label>
                <input type="tel" name="cell_number"
                       value="{{ old('cell_number') }}"
                       placeholder="e.g. 082 123 4567">
            </div>

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

        {{-- ── Dog Details ── --}}
        <div class="card">
            <p class="card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 9.5a5.5 5.5 0 0111 0v3a5.5 5.5 0 01-11 0v-3z"/><circle cx="8" cy="6" r="1.5" fill="currentColor" stroke="none"/><circle cx="16" cy="6" r="1.5" fill="currentColor" stroke="none"/></svg>
                Your Dog(s)
            </p>

            <template x-for="(dog, i) in dogs" :key="i">
                <div class="dog-card">
                    <div class="dog-card-title">
                        <span x-text="'Dog ' + (i + 1)"></span>
                        <button type="button" class="dog-remove"
                                x-show="dogs.length > 1"
                                @click="removeDog(i)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Remove
                        </button>
                    </div>

                    <div class="row-2">
                        <div class="field">
                            <label>Dog's name <span style="color:#dc2626">*</span></label>
                            <input type="text" :name="'dogs[' + i + '][name]'" required
                                   x-model="dog.name"
                                   placeholder="Buddy">
                        </div>
                        <div class="field">
                            <label>Breed</label>
                            <input type="text" :name="'dogs[' + i + '][breed]'"
                                   x-model="dog.breed"
                                   placeholder="e.g. Labrador">
                        </div>
                    </div>

                    <div class="row-2">
                        <div class="field">
                            <label>Date of birth</label>
                            <input type="date" :name="'dogs[' + i + '][date_of_birth]'"
                                   x-model="dog.date_of_birth"
                                   :max="new Date().toISOString().split('T')[0]">
                        </div>
                        <div class="field">
                            <label>Gender</label>
                            <select :name="'dogs[' + i + '][gender]'" x-model="dog.gender">
                                <option value="">Not specified</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label>Photo <span style="color:#9ca3af;font-weight:400">(optional)</span></label>
                        <label class="photo-label" :for="'photo_' + i">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <template x-if="!dog.photoName">
                                <span>Tap to add a photo</span>
                            </template>
                            <template x-if="dog.photoName">
                                <strong x-text="dog.photoName"></strong>
                            </template>
                        </label>
                        <input type="file" :id="'photo_' + i" :name="'dogs[' + i + '][photo]'"
                               accept="image/*" class="hidden"
                               @change="setPhoto(i, $event)">
                    </div>
                </div>
            </template>

            <button type="button" class="add-dog-btn" @click="addDog()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add another dog
            </button>
        </div>

        {{-- Submit --}}
        <button type="submit" class="submit-btn">Create My Account →</button>
        <p class="hint" style="text-align:center;margin-top:0.75rem;">
            Once you've signed up, your instructor will be in touch to confirm your class details.
        </p>

    </form>
</div>
</body>
</html>
