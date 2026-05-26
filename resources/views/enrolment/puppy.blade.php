@extends('layouts.app')

@section('title', 'Puppy Class Enrolment')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Puppy Class Enrolment</h1>
        <p class="page-subtitle">For puppies under 4 months old</p>
    </div>
    <a href="{{ route('enrol.start') }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content max-w-2xl">
    <form action="{{ route('enrol.puppy.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Handler info (if new) --}}
        @if(!$handler)
        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Your Details</h2>
            <div class="grid gap-4 md:grid-cols-2">
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
                <div>
                    <label class="form-label">Cell Number</label>
                    <input type="tel" name="cell_number" value="{{ old('cell_number') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Account Holder Name (if different)</label>
                    <input type="text" name="account_holder_name" value="{{ old('account_holder_name') }}" class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Occupation</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}" class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Vet Name &amp; Location</label>
                    <input type="text" name="vet_name_location" value="{{ old('vet_name_location') }}" class="form-input">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">How did you hear about us?</label>
                    <input type="text" name="hear_about_us" value="{{ old('hear_about_us') }}" class="form-input">
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="whatsapp_permission" value="1" class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span class="text-sm text-gray-600">I give permission to be added to WhatsApp class groups</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="social_media_permission" value="1" class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span class="text-sm text-gray-600">I give permission for photos/videos to be shared on social media</span>
                </label>
            </div>
        </div>
        @else
        <div class="card bg-brand/5">
            <p class="text-sm text-gray-600">Enrolling as <strong class="text-navy">{{ $handler->full_name }}</strong></p>
        </div>
        @endif

        {{-- Dog info --}}
        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Your Puppy</h2>

            @if($dogs->isNotEmpty())
            <div class="mb-4" x-data="{ useExisting: false }">
                <label class="flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" x-model="useExisting" class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span class="text-sm font-medium text-gray-700">Use an existing dog profile</span>
                </label>
                <div x-show="useExisting">
                    <select name="existing_dog_id" class="form-select">
                        <option value="">Select a dog...</option>
                        @foreach($dogs as $dog)
                        <option value="{{ $dog->id }}">{{ $dog->name }} ({{ $dog->breed ?? 'Unknown breed' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Puppy's Name <span class="text-red-500">*</span></label>
                    <input type="text" name="dog_name" value="{{ old('dog_name') }}" class="form-input" required>
                    @error('dog_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Breed</label>
                    <input type="text" name="dog_breed" value="{{ old('dog_breed') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="dog_dob" value="{{ old('dog_dob') }}" class="form-input" required>
                    <p class="text-xs text-gray-400 mt-1">Must be under 4 months at class start</p>
                    @error('dog_dob')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Microchip Number</label>
                    <input type="text" name="microchip_number" value="{{ old('microchip_number') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Vaccination Expiry</label>
                    <input type="date" name="vaccination_expiry" value="{{ old('vaccination_expiry') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Puppy Photo</label>
                    <input type="file" name="dog_photo" accept="image/*" capture="environment" class="text-sm text-gray-600">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Vaccination Card</label>
                    <input type="file" name="vaccination_card" accept="image/*,application/pdf" capture="environment" class="text-sm text-gray-600">
                </div>
            </div>
        </div>

        {{-- Agreements --}}
        @if(!$handler)
        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Agreements</h2>
            <div class="space-y-3">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="ground_rules_agreed" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand">
                    <span class="text-sm text-gray-600">I have read and agree to the McKaynine Ground Rules <span class="text-red-500">*</span></span>
                </label>
                @error('ground_rules_agreed')<p class="form-error">{{ $message }}</p>@enderror
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="terms_agreed" value="1" required class="mt-0.5 rounded border-gray-300 text-brand focus:ring-brand">
                    <span class="text-sm text-gray-600">I agree to the Terms &amp; Conditions <span class="text-red-500">*</span></span>
                </label>
                @error('terms_agreed')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>
        @endif

        <button type="submit" class="btn btn-primary w-full">Submit Enrolment</button>
    </form>
</div>
@endsection
