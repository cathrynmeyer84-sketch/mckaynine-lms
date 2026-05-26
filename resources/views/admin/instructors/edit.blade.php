<x-app-layout :title="'Edit: ' . $instructor->first_name . ' ' . $instructor->last_name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.instructors.show', $instructor) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Edit Instructor</h1>
                <p class="page-subtitle">{{ $instructor->first_name }} {{ $instructor->last_name }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.instructors.update', $instructor) }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="card">
                <h2 class="form-section-title">Instructor Details</h2>
                <div class="form-section space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $instructor->first_name) }}"
                                class="form-input w-full" required>
                            @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name', $instructor->last_name) }}"
                                class="form-input w-full" required>
                            @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $instructor->phone) }}"
                            class="form-input w-full">
                    </div>

                    <div>
                        <label class="form-label">Bio / Qualifications</label>
                        <textarea name="bio" rows="4" class="form-textarea w-full">{{ old('bio', $instructor->bio) }}</textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                            @checked(old('is_active', $instructor->is_active))>
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active (can be assigned to classes)</label>
                    </div>

                    <div>
                        <label class="label">Payment Frequency</label>
                        <div class="flex gap-4 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment_frequency" value="termly" class="text-navy"
                                    {{ old('payment_frequency', $instructor->payment_frequency ?? 'termly') === 'termly' ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">Termly</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment_frequency" value="monthly" class="text-navy"
                                    {{ old('payment_frequency', $instructor->payment_frequency ?? 'termly') === 'monthly' ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">Monthly</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Controls how instructor fee statements are generated and paid.</p>
                    </div>

                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary btn-lg">Save Changes</button>
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="btn-outline btn-lg">Cancel</a>
            </div>

        </form>
    </div>

</div>
</x-app-layout>
