@extends('layouts.app')

@section('title', 'Puppy Class Enrolment')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Puppy Class Enrolment</h1>
        <p class="page-subtitle">Honeydew — McKaynine Training</p>
    </div>
    <a href="{{ route('enrol.start') }}" class="btn btn-outline btn-sm">← Back</a>
</div>

<div class="page-content max-w-2xl mx-auto"
     x-data="{
         step: {{ $errors->any() ? $errors->has('first_name') || $errors->has('last_name') || $errors->has('email') || $errors->has('cell_number') ? 1 : ($errors->has('dog_name') || $errors->has('dog_dob') ? 2 : ($errors->has('vaccination_card_path') ? 3 : 4)) : 1 }},
         totalSteps: 4,
         stepLabels: ['About You', 'Your Pup', 'Health & Vaccinations', 'Checklist'],
         isAccountHolder: '{{ old('is_account_holder', 'yes') }}',
         hearOther: {{ in_array('other', old('hear_about_us_sources', [])) ? 'true' : 'false' }},
         vaccinationPath: '{{ old('vaccination_card_path', '') }}',
         vaccinationUploaded: {{ old('vaccination_card_path') ? 'true' : 'false' }},
         vaccinationUploading: false,
         vaccinationFileName: '',
         vaccinationError: '',
         async uploadVaccination(event) {
             const file = event.target.files[0];
             if (!file) return;
             this.vaccinationUploading = true;
             this.vaccinationUploaded = false;
             this.vaccinationError = '';
             this.vaccinationFileName = file.name;
             const fd = new FormData();
             fd.append('file', file);
             fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
             try {
                 const res = await fetch('{{ route('enrol.upload.vaccination') }}', { method: 'POST', body: fd });
                 const text = await res.text();
                 if (!res.ok) {
                     console.error('Upload error', res.status, text);
                     if (res.status === 413) throw new Error('File too large — please compress the image and try again.');
                     let msg = 'Upload failed — please try a smaller file or different format.';
                     try { msg = JSON.parse(text)?.message || msg; } catch {}
                     throw new Error(msg);
                 }
                 this.vaccinationPath = JSON.parse(text).path;
                 this.vaccinationUploaded = true;
             } catch (e) {
                 this.vaccinationError = e.message || 'Upload failed — please try again.';
                 this.vaccinationPath = '';
                 this.vaccinationUploaded = false;
             } finally {
                 this.vaccinationUploading = false;
             }
         },
         scrollTop() {
             const main = document.querySelector('main');
             if (main) main.scrollTo({ top: 0, behavior: 'smooth' });
             else window.scrollTo({ top: 0, behavior: 'smooth' });
         },
         nextStep() {
             const stepEl = document.getElementById('step-' + this.step);
             if (stepEl) {
                 const required = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
                 for (const input of required) {
                     if (!input.checkValidity()) {
                         input.reportValidity();
                         return;
                     }
                 }
                 if (this.step === 3 && !this.vaccinationPath) {
                     this.vaccinationError = 'Please upload your vaccination card before continuing.';
                     return;
                 }
             }
             this.vaccinationError = '';
             this.step++;
             this.scrollTop();
         },
         prevStep() {
             this.step--;
             this.scrollTop();
         }
     }">

    {{-- Step progress indicator --}}
    <div class="mb-8">
        {{-- Connector line layer --}}
        <div class="relative flex justify-between items-center">
            {{-- Background track --}}
            <div class="absolute inset-x-4 top-4 h-0.5 bg-gray-200 -translate-y-1/2"></div>
            {{-- Filled progress --}}
            <div class="absolute left-4 top-4 h-0.5 bg-brand -translate-y-1/2 transition-all duration-500"
                 :style="'width: calc(' + ((step - 1) / (totalSteps - 1) * 100) + '% - 2rem)'"></div>
            {{-- Step circles --}}
            <template x-for="(label, index) in stepLabels" :key="index">
                <div class="flex flex-col items-center relative z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
                         :class="step > index + 1
                             ? 'bg-brand text-white'
                             : (step === index + 1 ? 'bg-brand text-white ring-4 ring-brand/20' : 'bg-gray-100 text-gray-400 ring-1 ring-gray-200')">
                        <template x-if="step > index + 1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <template x-if="step <= index + 1">
                            <span x-text="index + 1"></span>
                        </template>
                    </div>
                    <span class="text-xs mt-2 text-center hidden sm:block transition-colors duration-300 w-20"
                          :class="step === index + 1 ? 'text-brand font-semibold' : (step > index + 1 ? 'text-brand/60' : 'text-gray-400')"
                          x-text="label"></span>
                </div>
            </template>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            Please review the highlighted fields and try again.
        </div>
    @endif

    <form action="{{ route('enrol.puppy.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ─────────────────────────────────────────────── --}}
        {{-- STEP 1 · About You --}}
        {{-- ─────────────────────────────────────────────── --}}
        <div id="step-1" x-show="step === 1"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">

            {{-- Class selection --}}
            <div class="card mb-4">
                <h2 class="text-base font-semibold text-navy mb-1">Which puppy class would you like to join? <span class="text-gray-400 font-normal text-xs">(optional)</span></h2>
                <select name="class_id" class="form-select mt-2">
                    <option value="">— Not sure yet —</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" @selected(old('class_id', $selectedClassId) == $c->id)>
                        {{ $c->name }}
                        @if($c->start_date) — {{ $c->start_date->format('j M Y') }}@endif
                        @if($c->location) · {{ explode(',', $c->location)[0] }}@endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="card">
                <h2 class="text-base font-semibold text-navy mb-4">About You</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="form-label">Full name <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">First <span class="text-red-500">*</span></p>
                                <input type="text" name="first_name" placeholder="First" value="{{ old('first_name') }}" class="form-input" required>
                                @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Last <span class="text-red-500">*</span></p>
                                <input type="text" name="last_name" placeholder="Last" value="{{ old('last_name') }}" class="form-input" required>
                                @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Cell <span class="text-red-500">*</span></label>
                        <input type="tel" name="cell_number" value="{{ old('cell_number') }}" class="form-input" required>
                        @error('cell_number')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Occupation <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                        <input type="text" name="occupation" value="{{ old('occupation') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Which vet do you use? <span class="text-red-500">*</span></label>
                        <input type="text" name="vet_name_location" value="{{ old('vet_name_location') }}" class="form-input" required>
                        @error('vet_name_location')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2 pt-2 border-t border-gray-100 mt-2">
                        <label class="form-label mb-2">Are you the person responsible for this account? <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 mt-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="is_account_holder" value="yes" x-model="isAccountHolder" class="text-brand focus:ring-brand" required>
                                Yes
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="is_account_holder" value="no" x-model="isAccountHolder" class="text-brand focus:ring-brand" required>
                                No
                            </label>
                        </div>
                        <div x-show="isAccountHolder === 'no'" x-cloak class="mt-4 grid gap-4 sm:grid-cols-2">
                            <p class="sm:col-span-2 text-xs text-gray-500">Please provide the details of the person responsible for the account.</p>
                            <div>
                                <label class="form-label">First name <span class="text-red-500">*</span></label>
                                <input type="text" name="account_holder_first_name" value="{{ old('account_holder_first_name') }}" class="form-input"
                                    :required="isAccountHolder === 'no'">
                                @error('account_holder_first_name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="form-label">Surname <span class="text-red-500">*</span></label>
                                <input type="text" name="account_holder_last_name" value="{{ old('account_holder_last_name') }}" class="form-input"
                                    :required="isAccountHolder === 'no'">
                                @error('account_holder_last_name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Email address <span class="text-red-500">*</span></label>
                                <input type="email" name="account_holder_email" value="{{ old('account_holder_email') }}" class="form-input"
                                    :required="isAccountHolder === 'no'">
                                @error('account_holder_email')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────── --}}
        {{-- STEP 2 · Your Pup --}}
        {{-- ─────────────────────────────────────────────── --}}
        <div id="step-2" x-show="step === 2" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="card">
                <h2 class="text-base font-semibold text-navy mb-4">About Your Pup</h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="form-label">Dog's name <span class="text-red-500">*</span></label>
                        <input type="text" name="dog_name" value="{{ old('dog_name') }}" class="form-input" required>
                        @error('dog_name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Date of birth <span class="text-red-500">*</span></label>
                        <input type="date" name="dog_dob" value="{{ old('dog_dob', $dob) }}" class="form-input" required>
                        @error('dog_dob')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Breed <span class="text-red-500">*</span></label>
                        <input type="text" name="dog_breed" value="{{ old('dog_breed') }}" class="form-input" required>
                        @error('dog_breed')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Gender <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 mt-2">
                            @foreach(['male' => 'Male', 'female' => 'Female'] as $value => $label)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="gender" value="{{ $value }}" {{ old('gender') === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Spay/Neuter --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="form-label">Spay / neuter status <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-5 mt-2">
                        @foreach(['when_old_enough' => 'When old enough', 'already_done' => 'Already done', 'not_planning' => 'Not planning to'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="spay_neuter_status" value="{{ $value }}" {{ old('spay_neuter_status') === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Origin --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="form-label">Acquired from <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                        @foreach([
                            'kusa_breeder'   => 'KUSA Breeder',
                            'spca_aacl'      => 'SPCA / AACL',
                            'rescue_org'     => 'Rescue org.',
                            'family_friends' => 'Family / friends',
                            'advert'         => 'Advert',
                            'born_in_home'   => 'Born in home',
                            'stray'          => 'Stray',
                            'other'          => 'Other',
                        ] as $value => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="origin_story" value="{{ $value }}" {{ old('origin_story') === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Age at acquisition --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="form-label">Age when you got them <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-5 mt-2">
                        @foreach([
                            'less_than_2_months' => '< 2 months',
                            '2_4_months'         => '2 – 4 months',
                            '4_12_months'        => '4 – 12 months',
                            'older_than_1_year'  => 'Older than 1 year',
                        ] as $value => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="age_when_acquired" value="{{ $value }}" {{ old('age_when_acquired') === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Other pets --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="form-label">Other pets at home <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-5 mt-2">
                        @foreach(['dogs' => 'Dogs', 'cats' => 'Cats', 'birds' => 'Birds', 'livestock' => 'Livestock', 'none' => 'None'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="animal_buddies_at_home[]" value="{{ $value }}"
                                {{ in_array($value, old('animal_buddies_at_home', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand focus:ring-brand">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Children --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="form-label">Children at home <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-5 mt-2">
                        @foreach(['babies_toddlers' => 'Babies / toddlers', 'children' => 'Children', 'teenagers' => 'Teenagers', 'none' => 'None'] as $value => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="young_children_at_home[]" value="{{ $value }}"
                                {{ in_array($value, old('young_children_at_home', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand focus:ring-brand">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Training goal --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <label class="form-label">Overall training goal <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-5 mt-2">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="training_goal" value="competitive_dog_sport" {{ old('training_goal') === 'competitive_dog_sport' ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                            Competitive dog sport
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="training_goal" value="chilled_canine_companion" {{ old('training_goal') === 'chilled_canine_companion' ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                            Chilled canine companion
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────── --}}
        {{-- STEP 3 · Health & Vaccinations --}}
        {{-- ─────────────────────────────────────────────── --}}
        <div id="step-3" x-show="step === 3" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="card space-y-6">
                <div>
                    <h2 class="text-base font-semibold text-navy mb-4">Socialisation</h2>
                    <p class="text-sm text-gray-500 mb-3">How does your pup get along with…</p>
                    <div class="space-y-3">
                        @foreach([
                            'socialisation_other_dogs'    => 'Other dogs',
                            'socialisation_other_animals' => 'Other animals',
                            'socialisation_people'        => 'People',
                        ] as $field => $label)
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-700 w-28 shrink-0">{{ $label }} <span class="text-red-500">*</span></span>
                            @foreach(['great' => 'Great', 'ok' => 'OK', 'not_good' => 'Not good'] as $value => $optLabel)
                            <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                                <input type="radio" name="{{ $field }}" value="{{ $value }}" {{ old($field) === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $optLabel }}
                            </label>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100" x-data="{ hasBehaviour: '{{ old('has_behaviour_problems', '') }}' }">
                    <h2 class="text-base font-semibold text-navy mb-3">Behaviour</h2>
                    <p class="text-sm text-gray-700 mb-2">Does your pup have any existing behaviour problems? <span class="text-red-500">*</span></p>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="has_behaviour_problems" value="0" x-model="hasBehaviour" class="text-brand focus:ring-brand" required>
                            No
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="has_behaviour_problems" value="1" x-model="hasBehaviour" class="text-brand focus:ring-brand" required>
                            Yes
                        </label>
                    </div>
                    <div x-show="hasBehaviour === '1'" x-cloak class="mt-3">
                        <label class="form-label">Please give more detail</label>
                        <textarea name="behaviour_problems_details" class="form-textarea" rows="2">{{ old('behaviour_problems_details') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100" x-data="{ hasHealth: '{{ old('has_health_issues', '') }}' }">
                    <h2 class="text-base font-semibold text-navy mb-3">Health</h2>
                    <p class="text-sm text-gray-700 mb-2">Does your pup have any existing health problems or disabilities? <span class="text-red-500">*</span></p>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="has_health_issues" value="0" x-model="hasHealth" class="text-brand focus:ring-brand" required>
                            No
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="has_health_issues" value="1" x-model="hasHealth" class="text-brand focus:ring-brand" required>
                            Yes
                        </label>
                    </div>
                    <div x-show="hasHealth === '1'" x-cloak class="mt-3">
                        <label class="form-label">Please give more detail</label>
                        <textarea name="health_issues" class="form-textarea" rows="2">{{ old('health_issues') }}</textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <h2 class="text-base font-semibold text-navy mb-1">Vaccination Card</h2>
                    <p class="text-sm text-gray-500 mb-4">Upload a photo or scan of your pup's vaccination card.</p>

                    <input type="hidden" name="vaccination_card_path" :value="vaccinationPath">

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Vaccination card <span class="text-red-500">*</span></label>

                            {{-- Success state --}}
                            <div x-show="vaccinationUploaded" x-cloak
                                 class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="vaccinationFileName || 'File uploaded'"></span>
                                </div>
                                <button type="button" @click="vaccinationUploaded = false; vaccinationPath = ''; vaccinationFileName = ''"
                                        class="text-green-600 hover:text-green-800 text-xs underline">Change</button>
                            </div>

                            {{-- Uploading state --}}
                            <div x-show="vaccinationUploading" x-cloak
                                 class="flex items-center gap-2 p-3 bg-brand/5 border border-brand/20 rounded-xl text-sm text-brand">
                                <svg class="w-4 h-4 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Uploading…
                            </div>

                            {{-- File picker --}}
                            <div x-show="!vaccinationUploaded && !vaccinationUploading">
                                <input type="file" accept="image/*,application/pdf,.heic,.heif"
                                    @change="uploadVaccination($event)"
                                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand/10 file:text-brand hover:file:bg-brand/20 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">Photo or PDF · max 10 MB</p>
                            </div>

                            <p x-show="vaccinationError" x-cloak x-text="vaccinationError" class="form-error mt-1"></p>
                            @error('vaccination_card_path')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────────── --}}
        {{-- STEP 4 · Checklist --}}
        {{-- ─────────────────────────────────────────────── --}}
        <div id="step-4" x-show="step === 4" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="card space-y-6">

                {{-- How did you hear --}}
                <div>
                    <h2 class="text-base font-semibold text-navy mb-3">How did you hear about us?</h2>
                    <p class="text-xs text-gray-400 mb-3">Tick all that apply</p>
                    <div class="flex flex-wrap gap-4">
                        @foreach([
                            'google'          => 'Google',
                            'my_vet'          => 'My vet',
                            'friends_family'  => 'Friends / family',
                            'breeder_shelter' => 'Breeder / shelter',
                            'been_before'     => 'Been before',
                        ] as $value => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="hear_about_us_sources[]" value="{{ $value }}"
                                {{ in_array($value, old('hear_about_us_sources', [])) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand focus:ring-brand">
                            {{ $label }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="hear_about_us_sources[]" value="other"
                                {{ in_array('other', old('hear_about_us_sources', [])) ? 'checked' : '' }}
                                x-model="hearOther" true-value="other" false-value=""
                                @change="hearOther = $event.target.checked"
                                class="rounded border-gray-300 text-brand focus:ring-brand">
                            Other
                        </label>
                    </div>
                    <div x-show="hearOther" x-cloak class="mt-3">
                        <input type="text" name="hear_about_us_other" value="{{ old('hear_about_us_other') }}"
                            placeholder="Please tell us how you heard about us"
                            class="form-input max-w-sm">
                    </div>
                </div>

                {{-- Consents --}}
                <div class="pt-4 border-t border-gray-100 space-y-5">
                    <h2 class="text-base font-semibold text-navy">Permissions</h2>
                    <div>
                        <p class="text-sm text-gray-700 mb-1">May we add you to a WhatsApp class group? <span class="text-red-500">*</span></p>
                        <p class="text-xs text-gray-400 mb-2">Used only for urgent class notifications. Your details are removed once training is complete.</p>
                        <div class="flex gap-5">
                            @foreach(['yes' => 'Yes', 'no' => 'No', 'unsure' => 'Unsure'] as $value => $label)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="whatsapp_consent" value="{{ $value }}" {{ old('whatsapp_consent') === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 mb-2">May we post graduation or class photos of you and your dog on our social media? <span class="text-red-500">*</span></p>
                        <div class="flex gap-5">
                            @foreach(['yes' => 'Yes', 'no' => 'No', 'unsure' => 'Unsure'] as $value => $label)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="photo_consent" value="{{ $value }}" {{ old('photo_consent') === $value ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Checklist --}}
                <div class="pt-4 border-t border-gray-100">
                    <h2 class="text-base font-semibold text-navy mb-3">Before you come to class…</h2>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="onlead_socialising" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I understand that on-lead socialising is not permitted <span class="text-red-500">*</span></span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="equipment_supervision" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I won't let my dog (or minors) go onto training equipment without supervision <span class="text-red-500">*</span></span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="training_equipment" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I've ensured my pup has the correct training equipment (refer to the <a href="#" class="underline text-brand">Info Pack</a> if unsure) <span class="text-red-500">*</span></span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="treats" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I'll bring plenty of small, soft training treats <span class="text-red-500">*</span></span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="waste_bags" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I'll bring bags for waste disposal <span class="text-red-500">*</span></span>
                        </label>
                    </div>
                </div>

                {{-- Agreement --}}
                <div class="pt-4 border-t border-gray-100">
                    <h2 class="text-base font-semibold text-navy mb-2">Agreement</h2>
                    <p class="text-sm text-gray-500 mb-4">By signing below I voluntarily agree to the McKaynine <a href="#" class="underline text-brand">Terms &amp; Conditions</a> (included in your <a href="#" class="underline text-brand">Info Pack</a> and available on our website).</p>
                    <div class="grid gap-4 sm:grid-cols-2 mb-4">
                        <div>
                            <label class="form-label">Full name <span class="text-red-500">*</span></label>
                            <input type="text" name="signature_name" value="{{ old('signature_name') }}" class="form-input" required>
                            @error('signature_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Date</label>
                            <input type="date" value="{{ today()->format('Y-m-d') }}" class="form-input bg-white" readonly>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="ground_rules_agreed" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I have read and agree to the <a href="#" class="underline text-brand">McKaynine Ground Rules</a> <span class="text-red-500">*</span></span>
                        </label>
                        @error('ground_rules_agreed')<p class="form-error">{{ $message }}</p>@enderror
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms_agreed" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I agree to the <a href="#" class="underline text-brand">McKaynine Terms &amp; Conditions</a> <span class="text-red-500">*</span></span>
                        </label>
                        @error('terms_agreed')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- Navigation buttons --}}
        <div class="flex items-center mt-6" :class="step > 1 ? 'justify-between' : 'justify-end'">
            <button type="button" x-show="step > 1" @click="prevStep()"
                    class="btn btn-outline">
                ← Back
            </button>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400" x-text="'Step ' + step + ' of ' + totalSteps"></span>
                <button type="button" x-show="step < totalSteps" @click="nextStep()"
                        class="btn btn-primary">
                    Next →
                </button>
                <button type="submit" x-show="step === totalSteps" x-cloak
                        class="btn btn-primary">
                    Submit Enrolment
                </button>
            </div>
        </div>

    </form>
</div>

@endsection
