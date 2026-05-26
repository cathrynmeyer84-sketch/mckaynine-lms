<x-app-layout :title="'Complete Your Enrolment'">
<div class="page-content max-w-2xl mx-auto"
    x-data="{
        step: 0,
        totalSteps: 3,

        photoPath: '',
        photoUploaded: false,
        photoUploading: false,
        photoFileName: '',
        photoError: '',

        vaccinationPath: '{{ old('vaccination_card_path', $dog->vaccination_card_path ?? '') }}',
        vaccinationUploaded: {{ ($dog->vaccination_card_path) ? 'true' : 'false' }},
        vaccinationUploading: false,
        vaccinationFileName: '{{ $dog->vaccination_card_path ? basename($dog->vaccination_card_path) : '' }}',
        vaccinationError: '',

        async uploadFile(event, type) {
            const file = event.target.files[0];
            if (!file) return;
            const isPhoto = type === 'photo';
            if (isPhoto) { this.photoUploading = true; this.photoError = ''; this.photoFileName = file.name; }
            else         { this.vaccinationUploading = true; this.vaccinationError = ''; this.vaccinationFileName = file.name; }

            try {
                const fd = new FormData();
                fd.append('file', file);
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                const url = isPhoto ? '{{ route('enrol.upload.dog-photo') }}' : '{{ route('enrol.upload.vaccination') }}';
                const res = await fetch(url, { method: 'POST', body: fd });
                if (!res.ok) throw new Error();
                const json = await res.json();
                if (isPhoto) { this.photoPath = json.path; this.photoUploaded = true; }
                else         { this.vaccinationPath = json.path; this.vaccinationUploaded = true; }
            } catch {
                if (isPhoto) { this.photoError = 'Upload failed. Please try again.'; this.photoUploaded = false; this.photoFileName = ''; }
                else         { this.vaccinationError = 'Upload failed. Please try again.'; this.vaccinationUploaded = false; this.vaccinationFileName = ''; }
            } finally {
                if (isPhoto) this.photoUploading = false;
                else         this.vaccinationUploading = false;
            }
        },

        nextStep() { if (this.step < this.totalSteps) { this.step++; document.querySelector('main')?.scrollTo({ top: 0, behavior: 'smooth' }); } },
        prevStep() { if (this.step > 1)  { this.step--; document.querySelector('main')?.scrollTo({ top: 0, behavior: 'smooth' }); } }
    }">

    {{-- Intro --}}
    <div x-show="step === 0">
        <div class="card text-center py-10">
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-navy mb-2">Complete Your Enrolment</h1>
            <p class="text-gray-600 mb-6">Welcome, {{ $handler->first_name }}! Just a few more details to get {{ $dog->name }} officially enrolled.</p>

            @if($outcome)
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium mb-6
                {{ $outcome === 'group_class' ? 'bg-green-100 text-green-800' : ($outcome === 'private_lessons' ? 'bg-amber/20 text-amber-900' : 'bg-gray-100 text-gray-700') }}">
                @if($outcome === 'group_class') Recommended: Group Classes
                @elseif($outcome === 'private_lessons') Recommended: Private Lessons
                @else Referral pathway @endif
            </div>
            @endif

            <div class="text-left bg-gray-50 rounded-xl p-5 mb-6 space-y-2 text-sm text-gray-700">
                <p class="font-medium text-gray-800">This short form covers:</p>
                <ul class="space-y-2 mt-2">
                    @foreach(['Your contact and vet details', 'A photo of ' . $dog->name . ' and vaccination info', 'Permissions and agreements'] as $item)
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>

            <button type="button" @click="step = 1; document.querySelector('main')?.scrollTo({ top: 0, behavior: 'smooth' })"
                class="btn btn-primary btn-lg w-full sm:w-auto">
                Let's Go →
            </button>
        </div>
    </div>

    {{-- Step indicator --}}
    <div class="mb-8" x-show="step > 0">
        <div class="relative flex justify-between items-center">
            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200 z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-brand z-0 transition-all duration-500"
                 :style="'width: ' + ((step - 1) / (totalSteps - 1) * 100) + '%'"></div>
            @for($i = 1; $i <= 3; $i++)
            <div class="relative z-10 flex flex-col items-center" :class="step >= {{ $i }} ? 'text-brand' : 'text-gray-300'">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300"
                     :class="step > {{ $i }} ? 'bg-brand border-brand text-white' : step === {{ $i }} ? 'bg-white border-brand text-brand' : 'bg-white border-gray-200 text-gray-300'">
                    <template x-if="step > {{ $i }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="step <= {{ $i }}"><span>{{ $i }}</span></template>
                </div>
            </div>
            @endfor
        </div>
        <div class="mt-3 text-center text-xs text-gray-400">Step <span x-text="step"></span> of 3</div>
    </div>

    <form action="{{ route('enrol.complete.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="vaccination_card_path" :value="vaccinationPath">
        <input type="hidden" name="dog_photo_path" :value="photoPath">

        {{-- ── Step 1: Your Details ────────────────────────────────────────── --}}
        <div x-show="step === 1" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">Your Details</h2>
                <p class="text-sm text-gray-500 mb-5">Please confirm and complete your contact information.</p>

                <div class="space-y-5">

                    {{-- Pre-filled read-only name --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">First Name</label>
                            <input type="text" value="{{ $handler->first_name }}" class="form-input bg-gray-100 text-gray-500" disabled>
                        </div>
                        <div>
                            <label class="form-label">Last Name</label>
                            <input type="text" value="{{ $handler->last_name }}" class="form-input bg-gray-100 text-gray-500" disabled>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Cell Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="cell_number" value="{{ old('cell_number', $handler->cell_number) }}" class="form-input" required>
                        @error('cell_number')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">Occupation</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $handler->occupation) }}" class="form-input" placeholder="e.g. Teacher, Engineer, Freelancer">
                    </div>

                    <div>
                        <label class="form-label">Account Holder Name <span class="text-sm font-normal text-gray-400">(if different from above)</span></label>
                        <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $handler->account_holder_name) }}" class="form-input" placeholder="Name on invoice / payment">
                    </div>

                    <div>
                        <label class="form-label">Vet Name &amp; Practice Location <span class="text-red-500">*</span></label>
                        <input type="text" name="vet_name_location" value="{{ old('vet_name_location', $handler->vet_name_location) }}" class="form-input" placeholder="e.g. Dr Smith — Constantia Vet Clinic">
                        @error('vet_name_location')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label">How did you hear about us?</label>
                        <input type="text" name="hear_about_us" value="{{ old('hear_about_us', $handler->hear_about_us) }}" class="form-input" placeholder="e.g. Instagram, friend recommendation, Google…">
                    </div>

                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" @click="nextStep()" class="btn btn-primary">Next →</button>
            </div>
        </div>

        {{-- ── Step 2: About {{ $dog->name }} ─────────────────────────────── --}}
        <div x-show="step === 2" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">About {{ $dog->name }}</h2>
                <p class="text-sm text-gray-500 mb-5">A few final details for {{ $dog->name }}'s profile.</p>

                <div class="space-y-5">

                    {{-- Pre-filled dog info --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Name</label>
                            <input type="text" value="{{ $dog->name }}" class="form-input bg-gray-100 text-gray-500" disabled>
                        </div>
                        <div>
                            <label class="form-label">Breed</label>
                            <input type="text" value="{{ $dog->breed }}" class="form-input bg-gray-100 text-gray-500" disabled>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Microchip Number</label>
                        <input type="text" name="microchip_number" value="{{ old('microchip_number', $dog->microchip_number) }}" class="form-input" placeholder="15-digit chip number">
                    </div>

                    <div>
                        <label class="form-label">Vaccination Expiry Date</label>
                        <input type="date" name="vaccination_expiry" value="{{ old('vaccination_expiry', $dog->vaccination_expiry_date?->format('Y-m-d')) }}" class="form-input">
                    </div>

                    {{-- Vaccination card (may already be uploaded) --}}
                    <div>
                        <label class="form-label">Vaccination Card</label>
                        <p class="text-xs text-gray-400 mb-2">
                            @if($dog->vaccination_card_path)
                                Already on file — upload a new one only if it has changed.
                            @else
                                JPG, PNG, HEIC or PDF. Max 10 MB.
                            @endif
                        </p>

                        <div x-show="!vaccinationUploaded && !vaccinationUploading">
                            <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-brand cursor-pointer transition-colors">
                                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-sm text-gray-500">Choose file or take a photo…</span>
                                <input type="file" class="hidden" accept="image/*,.heic,.heif,application/pdf" @change="uploadFile($event, 'vaccination')">
                            </label>
                        </div>
                        <div x-show="vaccinationUploading" class="flex items-center gap-2 text-sm text-gray-500 py-2">
                            <svg class="w-4 h-4 animate-spin text-brand" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            Uploading…
                        </div>
                        <div x-show="vaccinationUploaded" class="flex items-center gap-2 py-2">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm text-green-700 font-medium" x-text="vaccinationFileName || 'On file'"></span>
                            <button type="button" class="ml-auto text-xs text-gray-400 hover:text-gray-600 underline"
                                @click="vaccinationPath=''; vaccinationUploaded=false; vaccinationFileName=''">Change</button>
                        </div>
                        <p x-show="vaccinationError" class="form-error mt-1" x-text="vaccinationError"></p>
                    </div>

                    {{-- Dog photo --}}
                    <div>
                        <label class="form-label">Photo of {{ $dog->name }} <span class="text-sm font-normal text-gray-400">(optional)</span></label>
                        <p class="text-xs text-gray-400 mb-2">A recent photo for {{ $dog->name }}'s profile.</p>

                        <div x-show="!photoUploaded && !photoUploading">
                            <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-brand cursor-pointer transition-colors">
                                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-sm text-gray-500">Choose photo or take one now…</span>
                                <input type="file" class="hidden" accept="image/*" capture="environment" @change="uploadFile($event, 'photo')">
                            </label>
                        </div>
                        <div x-show="photoUploading" class="flex items-center gap-2 text-sm text-gray-500 py-2">
                            <svg class="w-4 h-4 animate-spin text-brand" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            Uploading…
                        </div>
                        <div x-show="photoUploaded" class="flex items-center gap-2 py-2">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm text-green-700 font-medium" x-text="photoFileName || 'Uploaded'"></span>
                            <button type="button" class="ml-auto text-xs text-gray-400 hover:text-gray-600 underline"
                                @click="photoPath=''; photoUploaded=false; photoFileName=''">Change</button>
                        </div>
                        <p x-show="photoError" class="form-error mt-1" x-text="photoError"></p>
                    </div>

                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="button" @click="prevStep()" class="btn btn-outline">← Back</button>
                <button type="button" @click="nextStep()" class="btn btn-primary">Next →</button>
            </div>
        </div>

        {{-- ── Step 3: Permissions & Agreements ───────────────────────────── --}}
        <div x-show="step === 3" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">Permissions &amp; Agreements</h2>
                <p class="text-sm text-gray-500 mb-5">Almost done — just a couple of last things.</p>

                <div class="space-y-6">

                    <div class="space-y-3">
                        <p class="form-label">Communication permissions</p>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="whatsapp_permission" value="1"
                                {{ old('whatsapp_permission', $handler->whatsapp_permission) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I give permission to be added to WhatsApp class groups</span>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="social_media_permission" value="1"
                                {{ old('social_media_permission', $handler->social_media_permission) ? 'checked' : '' }}
                                class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I give permission for photos/videos of me and my dog to be shared on social media</span>
                        </label>
                    </div>

                    <div class="border-t border-gray-100 pt-5 space-y-3">
                        <p class="form-label">Agreements <span class="text-red-500">*</span></p>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="ground_rules_agreed" value="1"
                                {{ old('ground_rules_agreed') ? 'checked' : '' }}
                                required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I have read and agree to the McKaynine Ground Rules <span class="text-red-500">*</span></span>
                        </label>
                        @error('ground_rules_agreed')<p class="form-error">{{ $message }}</p>@enderror

                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms_agreed" value="1"
                                {{ old('terms_agreed') ? 'checked' : '' }}
                                required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I agree to the McKaynine Terms &amp; Conditions <span class="text-red-500">*</span></span>
                        </label>
                        @error('terms_agreed')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="button" @click="prevStep()" class="btn btn-outline">← Back</button>
                <button type="submit" class="btn btn-primary">Submit Enrolment →</button>
            </div>
        </div>

    </form>
</div>
</x-app-layout>
