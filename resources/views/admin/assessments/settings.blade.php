<x-app-layout :title="'Assessment Settings'">
<div class="page-content max-w-2xl">

    <div class="page-header">
        <div>
            <h1 class="page-title">Assessment Settings</h1>
            <p class="page-subtitle">Configure location, instructions and outcome email templates</p>
        </div>
        <a href="{{ route('admin.assessments.index') }}" class="btn btn-outline">← Back</a>
    </div>

    <form method="POST" action="{{ route('admin.assessments.settings.save') }}" class="space-y-8">
        @csrf

        {{-- General Settings --}}
        <div class="card">
            <h2 class="font-semibold text-navy mb-1">General Settings</h2>
            <p class="text-xs text-gray-400 mb-6">Basic configuration for assessment notifications and logistics.</p>

            <div class="space-y-6">
                <div>
                    <label class="form-label">Admin Notification Email <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-400 mb-2">New assessment submissions will be emailed here.</p>
                    <input type="email" name="settings[admin_email]" value="{{ $settings['admin_email'] ?? '' }}" class="form-input" placeholder="admin@example.com">
                </div>

                <div>
                    <label class="form-label">Assessment Location</label>
                    <p class="text-xs text-gray-400 mb-2">Included in confirmation and reminder emails sent to handlers.</p>
                    <input type="text" name="settings[assessment_location]" value="{{ $settings['assessment_location'] ?? '' }}" class="form-input" placeholder="e.g. 123 Training Lane, Constantia, Cape Town">
                </div>

                <div>
                    <label class="form-label">On-the-day Instructions</label>
                    <p class="text-xs text-gray-400 mb-2">Shown in confirmation and reminder emails.</p>
                    <textarea name="settings[assessment_instructions]" rows="6" class="form-textarea"
                        placeholder="e.g.&#10;• Arrive 5 minutes before your slot&#10;• Keep your dog on lead at all times until instructed&#10;• Bring high-value treats your dog loves&#10;• No retractable leads please">{{ $settings['assessment_instructions'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-6">Save Settings</button>
    </form>

</div>
</x-app-layout>
