@extends('layouts.app')

@section('title', 'Complete Your Enrolment')

@section('content')
<div class="page-content max-w-xl mx-auto py-10 px-4">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-navy">Complete Your Enrolment</h1>
        <p class="text-gray-500 mt-1 text-sm">Just a few quick details to get {{ $assessmentRequest->dog?->name }} enrolled.</p>
    </div>

    @if($errors->any())
    <div class="alert alert-error mb-6">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('enrol.graduate.store', $assessmentRequest) }}">
        @csrf

        {{-- Dog summary --}}
        <div class="card mb-6">
            <h2 class="font-semibold text-navy mb-3">Enrolling</h2>
            <div class="flex items-center gap-4">
                @if($assessmentRequest->dog?->photo_path)
                <img src="{{ Storage::url($assessmentRequest->dog->photo_path) }}" alt="{{ $assessmentRequest->dog->name }}"
                    class="w-14 h-14 rounded-full object-cover border border-gray-200 flex-shrink-0">
                @else
                <div class="w-14 h-14 rounded-full bg-brand-beige flex items-center justify-center flex-shrink-0 border border-gray-200">
                    <svg class="w-7 h-7 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z"/>
                    </svg>
                </div>
                @endif
                <div>
                    <p class="font-semibold text-navy">{{ $assessmentRequest->dog?->name }}</p>
                    @if($assessmentRequest->dog?->breed)
                    <p class="text-xs text-gray-400">{{ $assessmentRequest->dog->breed }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Training goal --}}
        <div class="card mb-6">
            <h2 class="font-semibold text-navy mb-1">Training Goal</h2>
            <p class="text-xs text-gray-400 mb-4">What are you hoping to achieve with {{ $assessmentRequest->dog?->name }}?</p>
            <div class="space-y-3">
                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors
                    {{ old('training_goal') === 'chilled_canine_companion' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:bg-gray-50' }}">
                    <input type="radio" name="training_goal" value="chilled_canine_companion" class="mt-0.5 text-brand focus:ring-brand"
                        {{ old('training_goal') === 'chilled_canine_companion' ? 'checked' : '' }}>
                    <div>
                        <p class="text-sm font-medium text-navy">Chilled Canine Companion</p>
                        <p class="text-xs text-gray-400">A well-mannered dog who is easy to live with and take places</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors
                    {{ old('training_goal') === 'competitive_dog_sport' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:bg-gray-50' }}">
                    <input type="radio" name="training_goal" value="competitive_dog_sport" class="mt-0.5 text-brand focus:ring-brand"
                        {{ old('training_goal') === 'competitive_dog_sport' ? 'checked' : '' }}>
                    <div>
                        <p class="text-sm font-medium text-navy">Competitive Dog Sport</p>
                        <p class="text-xs text-gray-400">Working towards titles, competitions, or advanced sports</p>
                    </div>
                </label>
            </div>
            @error('training_goal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Vaccination card --}}
        @if(!$assessmentRequest->dog?->vaccination_card_path)
        <div class="card mb-6" x-data="{ uploading: false, uploaded: false, path: '' }">
            <h2 class="font-semibold text-navy mb-1">Vaccination Card</h2>
            <p class="text-xs text-gray-400 mb-4">Please upload a copy of {{ $assessmentRequest->dog?->name }}'s vaccination card showing their second multi-vaccination.</p>
            <input type="hidden" name="vaccination_card_path" x-model="path">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="file" accept="image/*,application/pdf" class="hidden"
                    @change="
                        uploading = true;
                        const fd = new FormData();
                        fd.append('file', $event.target.files[0]);
                        fd.append('_token', document.querySelector('meta[name=csrf-token]')?.content);
                        fetch('{{ route('enrol.upload.vaccination') }}', { method: 'POST', body: fd })
                            .then(r => r.json())
                            .then(d => { path = d.path; uploaded = true; uploading = false; })
                            .catch(() => { uploading = false; });
                    ">
                <span class="btn btn-secondary text-sm" x-text="uploading ? 'Uploading…' : uploaded ? 'Uploaded ✓' : 'Upload vaccination card'"></span>
            </label>
        </div>
        @else
        <input type="hidden" name="vaccination_card_path" value="">
        @endif

        {{-- Consents --}}
        <div class="card mb-6">
            <h2 class="font-semibold text-navy mb-4">A Couple of Quick Questions</h2>
            <div class="space-y-5">
                <div>
                    <label class="form-label">May we add you to our WhatsApp class group? <span class="text-red-400">*</span></label>
                    <div class="flex gap-3 mt-1">
                        @foreach(['yes' => 'Yes', 'no' => 'No', 'unsure' => 'Not sure yet'] as $val => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="whatsapp_consent" value="{{ $val }}" class="text-brand focus:ring-brand"
                                {{ old('whatsapp_consent') === $val ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    @error('whatsapp_consent')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">May we use photos/videos of {{ $assessmentRequest->dog?->name }} on social media? <span class="text-red-400">*</span></label>
                    <div class="flex gap-3 mt-1">
                        @foreach(['yes' => 'Yes', 'no' => 'No', 'unsure' => 'Not sure yet'] as $val => $label)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="photo_consent" value="{{ $val }}" class="text-brand focus:ring-brand"
                                {{ old('photo_consent') === $val ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    @error('photo_consent')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-full">Submit Enrolment →</button>
    </form>
</div>
@endsection
