@extends('layouts.app')

@section('title', 'Assessment Questionnaire')

@section('content')
<div class="page-content max-w-2xl mx-auto"
    x-data="{
        step: 0,
        totalSteps: 5,

        trainingGoalsOther: {{ old('training_goals_other') ? 'true' : 'false' }},
        healthConcernsYes: '{{ old('health_concerns_yes', '') }}',
        aggressionTargets: @json(old('aggression_targets', [])),
        priorTrainingYes: '{{ old('prior_training_yes', '') }}',
        checklistError: '',

        vaccinationPath: '{{ old('vaccination_card_path', '') }}',
        vaccinationUploaded: {{ old('vaccination_card_path') ? 'true' : 'false' }},
        vaccinationUploading: false,
        vaccinationFileName: '{{ old('vaccination_card_path') ? basename(old('vaccination_card_path')) : '' }}',
        vaccinationError: '',

        async uploadVaccination(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.vaccinationUploading = true;
            this.vaccinationError = '';
            this.vaccinationFileName = file.name;
            try {
                const fd = new FormData();
                fd.append('file', file);
                fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                const res = await fetch('{{ route('enrol.upload.vaccination') }}', { method: 'POST', body: fd });
                const text = await res.text();
                if (!res.ok) throw new Error('Upload failed');
                this.vaccinationPath = JSON.parse(text).path;
                this.vaccinationUploaded = true;
            } catch(e) {
                this.vaccinationError = 'Upload failed. Please try again.';
                this.vaccinationPath = '';
                this.vaccinationUploaded = false;
                this.vaccinationFileName = '';
            } finally {
                this.vaccinationUploading = false;
            }
        },

        nextStep() {
            if (this.step < this.totalSteps) {
                this.step++;
                document.querySelector('main')?.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        submitForm(formEl) {
            this.checklistError = '';
            if (!this.vaccinationPath) {
                this.vaccinationError = 'Please upload your vaccination card before submitting.';
                return;
            }
            const boxes = ['checklist_collar','checklist_treats','checklist_follow_staff','checklist_no_onlead','checklist_clean_up'];
            const allChecked = boxes.every(name => formEl.querySelector(`[name='${name}']`)?.checked);
            if (!allChecked) {
                this.checklistError = 'Please confirm all items above before submitting.';
                return;
            }
            formEl.submit();
        },

        prevStep() {
            if (this.step > 1) {
                this.step--;
                document.querySelector('main')?.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }">

    {{-- ── Intro page ─────────────────────────────────────────────────── --}}
    <div x-show="step === 0" x-cloak>
        <div class="card text-center py-10">
            <div class="w-16 h-16 rounded-full bg-brand/10 flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-navy mb-2">Assessment Questionnaire</h1>
            <p class="text-gray-600 mb-6">Thank you for your interest in training with us!</p>

            <div class="text-left bg-gray-50 rounded-xl p-5 mb-6 space-y-3 text-sm text-gray-700">
                <p class="font-medium text-gray-800">This short form:</p>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Helps us get to know your dog a little before your assessment
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Gives us a heads-up about any specific needs
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Introduces possible training pathways
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Outlines what the assessment involves
                    </li>
                </ul>
            </div>

            <p class="text-sm text-gray-600 mb-2">
                Our goal is always to recommend the best option for your dog right now — whether that's group classes, private lessons, or a behaviour consultation. Some dogs thrive in a group setting straight away; others need a more tailored approach to build confidence and success.
            </p>
            <p class="text-sm text-gray-500 mb-6 italic">
                There are no right or wrong answers — just honest ones that help us help you and your dog.
            </p>

            <p class="text-xs text-gray-400 mb-6">
                By submitting this form you agree to our Privacy Policy.
            </p>

            <button type="button" @click="step = 1; document.querySelector('main')?.scrollTo({ top: 0, behavior: 'smooth' })" class="btn btn-primary btn-lg w-full sm:w-auto">
                Get Started →
            </button>
        </div>
    </div>

    {{-- Step indicator --}}
    <div class="mb-8" x-show="step > 0">
        <div class="relative flex justify-between items-center">
            {{-- Background track --}}
            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200 z-0"></div>
            {{-- Animated fill --}}
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-brand z-0 transition-all duration-500"
                 :style="'width: ' + ((step - 1) / (totalSteps - 1) * 100) + '%'"></div>
            {{-- Circles --}}
            @for($i = 1; $i <= 5; $i++)
            <div class="relative z-10 flex flex-col items-center"
                 :class="step >= {{ $i }} ? 'text-brand' : 'text-gray-300'">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition-all duration-300"
                     :class="step > {{ $i }} ? 'bg-brand border-brand text-white' : step === {{ $i }} ? 'bg-white border-brand text-brand' : 'bg-white border-gray-200 text-gray-300'">
                    <template x-if="step > {{ $i }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="step <= {{ $i }}">
                        <span>{{ $i }}</span>
                    </template>
                </div>
            </div>
            @endfor
        </div>
        <div class="mt-3 text-center text-xs text-gray-400">
            Step <span x-text="step"></span> of {{ 5 }}
        </div>
    </div>

    <form action="{{ route('enrol.assessment.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="vaccination_card_path" :value="vaccinationPath">

        {{-- ── Step 1: About You & Your Dog ──────────────────────────────── --}}
        <div x-show="step === 1" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">About You &amp; Your Dog</h2>
                <p class="text-sm text-gray-500 mb-5">Let's start with the basics.</p>

                <div class="space-y-4">
                    <div>
                        <label class="form-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-input" required>
                            @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-input" required>
                            @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Cell Number <span class="text-red-500">*</span></label>
                        <input type="tel" name="cell_number" value="{{ old('cell_number') }}" class="form-input" required>
                        @error('cell_number')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <hr class="border-gray-100">

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Dog's Name <span class="text-red-500">*</span></label>
                            <input type="text" name="dog_name" value="{{ old('dog_name') }}" class="form-input" required>
                            @error('dog_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Breed / Mix <span class="text-red-500">*</span></label>
                            <input type="text" name="dog_breed" value="{{ old('dog_breed') }}" class="form-input" placeholder="e.g. Border Collie Mix" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Dog's Age <span class="text-red-500">*</span></label>
                        <input type="text" name="dog_age_description" value="{{ old('dog_age_description') }}" class="form-input" placeholder="e.g. 8 months, 2 years" required>
                        @if($dob)
                        <input type="hidden" name="dog_dob" value="{{ $dob }}">
                        @endif
                    </div>

                    <div>
                        <label class="form-label">Gender &amp; Reproductive Status <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            @foreach(['Male — intact' => 'Male — intact', 'Male — neutered' => 'Male — neutered', 'Female — intact' => 'Female — intact', 'Female — spayed' => 'Female — spayed'] as $val => $lbl)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="gender_repro_status" value="{{ $val }}" {{ old('gender_repro_status') === $val ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $lbl }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Where did you get your dog? <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            @foreach(['Registered breeder', 'Unregistered breeder', 'Rescue', 'Friends / family', 'Self-bred', 'Stray'] as $opt)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="where_got_dog" value="{{ $opt }}" {{ old('where_got_dog') === $opt ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $opt }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="form-label">How old was your dog when you acquired them? <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4 mt-1">
                            @foreach(['Under 2 months' => 'Under 2 months', '2 – 6 months' => '2 – 6 months', '6 – 18 months' => '6 – 18 months', 'Over 18 months' => 'Over 18 months'] as $val => $lbl)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="age_acquired" value="{{ $val }}" {{ old('age_acquired') === $val ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $lbl }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="form-label">How long have you had your dog? <span class="text-red-500">*</span></label>
                        <input type="text" name="how_long_had_dog" value="{{ old('how_long_had_dog') }}" class="form-input" placeholder="e.g. 6 months" required>
                    </div>

                    <div>
                        <label class="form-label">Any health concerns we should know about? <span class="text-red-500">*</span></label>
                        <div class="flex gap-5 mt-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="health_concerns_yes" value="yes" x-model="healthConcernsYes" {{ old('health_concerns_yes') === 'yes' ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                Yes
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="health_concerns_yes" value="no" x-model="healthConcernsYes" {{ old('health_concerns_yes') === 'no' ? 'checked' : '' }} class="text-brand focus:ring-brand">
                                No
                            </label>
                        </div>
                        <div x-show="healthConcernsYes === 'yes'" class="mt-2">
                            <label class="form-label text-xs text-gray-500 mb-1">Please tell us about them <span class="text-red-500">*</span></label>
                            <textarea name="health_concerns" rows="2" class="form-textarea" placeholder="Allergies, injuries, medications…"
                                :required="healthConcernsYes === 'yes'"
                                :disabled="healthConcernsYes !== 'yes'">{{ old('health_concerns') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" @click="nextStep()" class="btn btn-primary">Next →</button>
            </div>
        </div>

        {{-- ── Step 2: What You're Hoping to Achieve ─────────────────────── --}}
        <div x-show="step === 2" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">What You're Hoping to Achieve</h2>
                <p class="text-sm text-gray-500 mb-5">Tell us about your training goals.</p>

                <div class="space-y-5">
                    <div>
                        <label class="form-label">What are your main training goals? <span class="text-sm font-normal text-gray-400">(tick all that apply)</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                            @foreach([
                                'Basic manners / obedience',
                                'CGC Bronze title',
                                'Elementary Obedience title',
                                'Behaviour / reactivity issues',
                                'Socialisation',
                                'Fun &amp; bonding',
                                'Sport / competition',
                                'Other',
                            ] as $goal)
                            <label class="flex items-center gap-2 cursor-pointer" @if($goal === 'Other') @click="trainingGoalsOther = $event.target.checked" @endif>
                                <input type="checkbox" name="training_goals[]" value="{{ $goal }}"
                                    {{ in_array($goal, old('training_goals', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-brand focus:ring-brand"
                                    @if($goal === 'Other') x-model="trainingGoalsOther" :value="trainingGoalsOther" @endif>
                                <span class="text-sm text-gray-700">{!! $goal !!}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="trainingGoalsOther">
                        <label class="form-label">Please describe your "Other" goal</label>
                        <input type="text" name="training_goals_other_detail" value="{{ old('training_goals_other_detail') }}" class="form-input">
                    </div>

                    <div>
                        <label class="form-label">What outcomes are you hoping for? <span class="text-red-500">*</span></label>
                        <textarea name="desired_outcomes" rows="3" class="form-textarea" placeholder="e.g. A dog I can walk without them pulling, that comes when called…" required>{{ old('desired_outcomes') }}</textarea>
                    </div>

                    <div>
                        <label class="form-label">Are there any specific issues you'd like help with? <span class="text-red-500">*</span></label>
                        <textarea name="specific_issues" rows="3" class="form-textarea" placeholder="e.g. Pulls on lead, jumps up, reactive to other dogs, separation anxiety…" required>{{ old('specific_issues') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="button" @click="prevStep()" class="btn btn-outline">← Back</button>
                <button type="button" @click="nextStep()" class="btn btn-primary">Next →</button>
            </div>
        </div>

        {{-- ── Step 3: Important Info ─────────────────────────────────────── --}}
        <div x-show="step === 3" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">Important Info for Us to Know</h2>
                <p class="text-sm text-gray-500 mb-5">This helps us prepare for your dog's assessment.</p>

                <div class="space-y-6">
                    <div>
                        <label class="form-label">How does your dog respond to new people? <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400 mb-3">1 = Very friendly &nbsp;·&nbsp; 5 = Very nervous or aggressive</p>
                        <div class="flex gap-3">
                            @for($i = 1; $i <= 5; $i++)
                            <label class="flex flex-col items-center gap-1 cursor-pointer">
                                <input type="radio" name="response_to_new_people" value="{{ $i }}" {{ old('response_to_new_people') == $i ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                <span class="text-sm font-medium text-gray-600">{{ $i }}</span>
                            </label>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <label class="form-label">How does your dog behave around unfamiliar dogs? <span class="text-red-500">*</span></label>
                        <div class="space-y-2 mt-1">
                            @foreach(['Very sociable — loves other dogs', 'Not really interested in other dogs', 'Scared and nervous around other dogs', 'Aggressively toward other dogs', "I don't know"] as $opt)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="behaviour_around_dogs" value="{{ $opt }}" {{ old('behaviour_around_dogs') === $opt ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $opt }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Has your dog ever shown aggression? <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400 mb-2">Tick all that apply</p>
                        <div class="space-y-2">
                            @foreach(['No — never shown aggression', 'Yes — toward a person', 'Yes — toward another dog'] as $opt)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="aggression_targets[]" value="{{ $opt }}"
                                    x-model="aggressionTargets"
                                    {{ in_array($opt, old('aggression_targets', [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-brand focus:ring-brand">
                                {{ $opt }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="aggressionTargets.some(v => v.startsWith('Yes'))">
                        <label class="form-label">Please give details <span class="text-red-500">*</span></label>
                        <textarea name="aggression_details" rows="2" class="form-textarea" placeholder="Please describe the situation(s)…"
                            :required="aggressionTargets.some(v => v.startsWith('Yes'))"
                            :disabled="!aggressionTargets.some(v => v.startsWith('Yes'))">{{ old('aggression_details') }}</textarea>
                    </div>

                    <div>
                        <label class="form-label">Has your dog had any formal training before? <span class="text-red-500">*</span></label>
                        <div class="flex gap-5 mt-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="prior_training_yes" value="yes" x-model="priorTrainingYes" {{ old('prior_training_yes') === 'yes' ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                Yes
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="prior_training_yes" value="no" x-model="priorTrainingYes" {{ old('prior_training_yes') === 'no' ? 'checked' : '' }} class="text-brand focus:ring-brand">
                                No
                            </label>
                        </div>
                        <div x-show="priorTrainingYes === 'yes'" class="mt-2">
                            <label class="form-label text-xs text-gray-500 mb-1">What training has your dog had? <span class="text-red-500">*</span></label>
                            <textarea name="prior_training" rows="2" class="form-textarea" placeholder="e.g. Puppy classes, private lessons, online courses…"
                                :required="priorTrainingYes === 'yes'"
                                :disabled="priorTrainingYes !== 'yes'">{{ old('prior_training') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">How does your dog cope in busy or distracting environments? <span class="text-red-500">*</span></label>
                        <div class="space-y-2 mt-1">
                            @foreach(['Very comfortable', 'Generally okay but can be unsure initially', 'Very anxious or reactive', 'Not sure'] as $opt)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="comfort_in_busy_environments" value="{{ $opt }}" {{ old('comfort_in_busy_environments') === $opt ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $opt }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="button" @click="prevStep()" class="btn btn-outline">← Back</button>
                <button type="button" @click="nextStep()" class="btn btn-primary">Next →</button>
            </div>
        </div>

        {{-- ── Step 4: What the Assessment Involves ──────────────────────── --}}
        <div x-show="step === 4" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">What the Assessment Involves</h2>
                <p class="text-sm text-gray-500 mb-5">So we're on the same page before we meet.</p>

                <div class="space-y-5">
                    <div class="bg-gray-50 rounded-xl p-5 space-y-3 text-sm text-gray-700">
                        <p class="font-medium text-gray-800">What to expect at your assessment:</p>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                A short one-on-one session with one of our trainers (approx. 30–45 min)
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                We'll observe your dog's temperament, social skills, and basic responses
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                We may handle your dog briefly and introduce them to a calm, familiar dog
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                We'll discuss your goals and recommend the most suitable class or programme
                            </li>
                        </ul>
                    </div>

                    <div>
                        <label class="form-label">Any concerns or questions about the assessment? <span class="text-sm font-normal text-gray-400">(optional)</span></label>
                        <textarea name="comfortable_with_assessment" rows="3" class="form-textarea" placeholder="Feel free to share anything that might help us prepare…">{{ old('comfortable_with_assessment') }}</textarea>
                    </div>

                    <div>
                        <label class="form-label">Are you open to our recommendation? <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400 mb-2">We'll always explain our reasoning and discuss options with you.</p>
                        <div class="space-y-2">
                            @foreach(['Yes — I trust your recommendation' => 'Yes — I trust your recommendation', 'Group classes only — I\'d prefer not to do private lessons' => "Group classes only — I'd prefer not to do private lessons", 'Not sure yet — I\'d like to discuss first' => "Not sure yet — I'd like to discuss first"] as $val => $lbl)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="open_to_recommendation" value="{{ $val }}" {{ old('open_to_recommendation') === $val ? 'checked' : '' }} class="text-brand focus:ring-brand" required>
                                {{ $lbl }}
                            </label>
                            @endforeach
                        </div>
                        @error('open_to_recommendation')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="button" @click="prevStep()" class="btn btn-outline">← Back</button>
                <button type="button" @click="nextStep()" class="btn btn-primary">Next →</button>
            </div>
        </div>

        {{-- ── Step 5: A Few Last Things ──────────────────────────────────── --}}
        <div x-show="step === 5" x-cloak>
            <div class="card">
                <h2 class="text-lg font-bold text-navy mb-1">A Few Last Things</h2>
                <p class="text-sm text-gray-500 mb-5">Almost done — just a few housekeeping items.</p>

                <div class="space-y-6">
                    {{-- Checklist --}}
                    <div>
                        <p class="form-label mb-3">Please confirm the following: <span class="text-red-500">*</span></p>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="checklist_collar" value="1" {{ old('checklist_collar') ? 'checked' : '' }} required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                                <span class="text-sm text-gray-700">My dog will arrive on a properly fitted collar or harness with a leash</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="checklist_treats" value="1" {{ old('checklist_treats') ? 'checked' : '' }} required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                                <span class="text-sm text-gray-700">I will bring high-value treats my dog enjoys</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="checklist_follow_staff" value="1" {{ old('checklist_follow_staff') ? 'checked' : '' }} required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                                <span class="text-sm text-gray-700">I will follow all staff directions during the assessment</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="checklist_no_onlead" value="1" {{ old('checklist_no_onlead') ? 'checked' : '' }} required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                                <span class="text-sm text-gray-700">I understand there will be no on-lead socialisation with other dogs unless directed</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="checklist_clean_up" value="1" {{ old('checklist_clean_up') ? 'checked' : '' }} required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                                <span class="text-sm text-gray-700">I will clean up after my dog if needed</span>
                            </label>
                        </div>
                    </div>

                    {{-- Vaccination card --}}
                    <div>
                        <label class="form-label">Vaccination Card <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-400 mb-2">Photo or file — JPG, PNG, HEIC, PDF accepted. Max 10 MB.</p>

                        <div x-show="!vaccinationUploaded && !vaccinationUploading">
                            <label class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-300 hover:border-brand cursor-pointer transition-colors">
                                <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm text-gray-500">Choose file or take a photo…</span>
                                <input type="file" class="hidden" accept="image/*,.heic,.heif,application/pdf" @change="uploadVaccination($event)">
                            </label>
                        </div>

                        <div x-show="vaccinationUploading" class="flex items-center gap-2 text-sm text-gray-500 py-2">
                            <svg class="w-4 h-4 animate-spin text-brand" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            Uploading…
                        </div>

                        <div x-show="vaccinationUploaded" class="flex items-center gap-2 py-2">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-green-700 font-medium" x-text="vaccinationFileName || 'Uploaded'"></span>
                            <button type="button" class="ml-auto text-xs text-gray-400 hover:text-gray-600 underline"
                                @click="vaccinationPath=''; vaccinationUploaded=false; vaccinationFileName=''; vaccinationError=''">
                                Change
                            </button>
                        </div>

                        <p x-show="vaccinationError" class="form-error mt-1" x-text="vaccinationError"></p>
                        @error('vaccination_card_path')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Additional notes --}}
                    <div>
                        <label class="form-label">Anything else you'd like us to know? <span class="text-sm font-normal text-gray-400">(optional)</span></label>
                        <textarea name="additional_notes" rows="3" class="form-textarea" placeholder="Anything that might help us prepare for your visit…">{{ old('additional_notes') }}</textarea>
                    </div>

                    {{-- T&C --}}
                    <div class="border-t border-gray-100 pt-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms_agreed" value="1" {{ old('terms_agreed') ? 'checked' : '' }} required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand shrink-0">
                            <span class="text-sm text-gray-700">I confirm that all information provided is accurate, and I agree to the McKaynine Terms &amp; Conditions and Ground Rules <span class="text-red-500">*</span></span>
                        </label>
                        @error('terms_agreed')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <p x-show="checklistError" class="text-sm text-red-600 mt-2" x-text="checklistError"></p>

            <div class="flex justify-between mt-4">
                <button type="button" @click="prevStep()" class="btn btn-outline">← Back</button>
                <button type="button" @click="submitForm($el.closest('form'))" class="btn btn-primary">Submit →</button>
            </div>
        </div>

    </form>
</div>
@endsection
