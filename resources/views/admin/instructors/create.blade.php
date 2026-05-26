<x-app-layout :title="'Add Instructor'">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.instructors.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Add Instructor</h1>
                <p class="page-subtitle">Create a new instructor account</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.instructors.store') }}" class="space-y-6">
            @csrf

            <div class="card">
                <h2 class="form-section-title">Instructor Details</h2>
                <div class="form-section space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="first_name">First Name <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                class="form-input w-full" required>
                            @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label" for="last_name">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                class="form-input w-full" required>
                            @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="email">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-input w-full" required>
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-400 mt-1">A login account will be created with this email.</p>
                    </div>

                    <div>
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                            class="form-input w-full" placeholder="+27...">
                        @error('phone')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="form-label" for="bio">Bio / Qualifications</label>
                        <textarea id="bio" name="bio" rows="4" class="form-textarea w-full"
                            placeholder="Instructor background, qualifications, specialities...">{{ old('bio') }}</textarea>
                        @error('bio')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            <div class="card bg-amber/5 border border-amber/20">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-amber flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-sm text-gray-700">
                        <p class="font-semibold text-amber">Account Creation Note</p>
                        <p class="mt-1">A user account will be created with a random password. The instructor will need to use "Forgot Password" to set their own password, or you can send them an invitation email manually.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary btn-lg">Create Instructor</button>
                <a href="{{ route('admin.instructors.index') }}" class="btn-outline btn-lg">Cancel</a>
            </div>

        </form>
    </div>

</div>
</x-app-layout>
