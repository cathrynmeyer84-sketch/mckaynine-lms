<x-app-layout title="My Profile">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">Update your photo, bio and contact details</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data"
          x-data="{ previewUrl: null }">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left col: Photo --}}
            <div class="space-y-6">
                <div class="card text-center">
                    <h2 class="card-title mb-4">Profile Photo</h2>

                    {{-- Current / preview --}}
                    <div class="flex justify-center mb-4">
                        <div class="w-28 h-28 rounded-2xl overflow-hidden bg-navy flex items-center justify-center flex-shrink-0">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewUrl">
                                @if($instructor->profile_photo_path)
                                <img src="{{ Storage::url($instructor->profile_photo_path) }}"
                                     alt="{{ $instructor->first_name }}"
                                     class="w-full h-full object-cover">
                                @else
                                <span class="text-white text-4xl font-bold">{{ substr($instructor->first_name, 0, 1) }}</span>
                                @endif
                            </template>
                        </div>
                    </div>

                    <label class="btn-outline btn-sm cursor-pointer inline-block">
                        Choose Photo
                        <input type="file" name="photo" accept="image/*" class="hidden"
                            @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                    </label>
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG or WebP · max 4 MB</p>
                    @error('photo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Right col: Details --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Bio --}}
                <div class="card">
                    <h2 class="card-title mb-4">About Me</h2>
                    <div>
                        <label class="form-label">Bio</label>
                        <textarea name="bio" rows="5" class="input mt-1"
                            placeholder="Tell handlers and students a bit about yourself — your background, specialisms, training philosophy…"
                            maxlength="2000">{{ old('bio', $instructor->bio) }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Shown on your public instructor profile. Max 2000 characters.</p>
                        @error('bio')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Personal details --}}
                <div class="card">
                    <h2 class="card-title mb-4">Personal Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="form-label">Birthday</label>
                            <input type="date" name="birthday" class="input mt-1"
                                value="{{ old('birthday', $instructor->birthday?->format('Y-m-d')) }}"
                                max="{{ today()->subDay()->toDateString() }}">
                            <p class="text-xs text-gray-400 mt-1">Only visible to you and admin.</p>
                            @error('birthday')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="phone" class="input mt-1"
                                value="{{ old('phone', $instructor->phone) }}"
                                placeholder="e.g. 082 000 0000" maxlength="30">
                            <p class="text-xs text-gray-400 mt-1">Only visible to admin — not shown to handlers.</p>
                            @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>

                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">Save Profile</button>
                </div>
            </div>

        </div>
    </form>

</div>
</x-app-layout>
