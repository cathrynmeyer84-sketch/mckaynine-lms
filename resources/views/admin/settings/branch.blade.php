@extends('layouts.app')
@section('title', 'Branch Settings')
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Branch Settings</h1>
        <p class="page-subtitle">Contact details, banking, and fees shown on public info pages</p>
    </div>
</div>

<div class="page-content">
@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.branch-settings.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card mb-6">
        <h2 class="card-title mb-4">Branch Details</h2>
        <div class="space-y-4">
            <div>
                <label class="label">Branch Trading Name</label>
                <p class="text-xs text-gray-400 mt-0.5 mb-1">The name your branch trades as, e.g. McKaynine Honeydew</p>
                <input type="text" name="branch_name" class="input" placeholder="McKaynine Honeydew"
                    value="{{ old('branch_name', $branch->branch_name) }}">
            </div>
            <div>
                <label class="label">Address</label>
                <textarea name="address" rows="3" class="input" placeholder="123 Main Road, Kyalami, Midrand">{{ old('address', $branch->address) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Email Address</label>
                    <input type="email" name="email" class="input" placeholder="info@mckaynine.co.za"
                        value="{{ old('email', $branch->email) }}">
                </div>
                <div>
                    <label class="label">Contact Number</label>
                    <input type="text" name="phone" class="input" placeholder="+27 11 000 0000"
                        value="{{ old('phone', $branch->phone) }}">
                </div>
            </div>
            <div>
                <label class="label">Website</label>
                <input type="text" name="website" class="input" placeholder="www.mckaynine.co.za"
                    value="{{ old('website', $branch->website) }}">
            </div>
        </div>
    </div>

    {{-- Hero Image --}}
    <div class="card mb-6" x-data="{ removing: false }">
        <h2 class="card-title mb-1">Home Page Banner Image</h2>
        <p class="text-xs text-gray-400 mb-4">Shown as the background photo on the public home page. Recommended: landscape, at least 1400×800px, JPG or PNG.</p>

        @if($branch->hero_image_path)
        <div class="mb-4">
            <div class="relative inline-block rounded-xl overflow-hidden border border-gray-200" style="max-width: 480px;">
                <img src="{{ Storage::url($branch->hero_image_path) }}" alt="Current banner" class="w-full h-40 object-cover">
                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <span class="text-white text-xs font-semibold">Current banner</span>
                </div>
            </div>
            <div class="mt-2 flex items-center gap-3">
                <label class="flex items-center gap-2 text-xs text-red-500 cursor-pointer">
                    <input type="checkbox" name="remove_hero_image" value="1" x-model="removing" class="rounded text-red-500">
                    Remove current image
                </label>
            </div>
        </div>
        @endif

        <div x-show="!removing">
            <label class="label">{{ $branch->hero_image_path ? 'Replace image' : 'Upload image' }}</label>
            <input type="file" name="hero_image" accept="image/jpeg,image/png,image/webp"
                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-navy/5 file:text-navy hover:file:bg-navy/10 cursor-pointer">
            @error('hero_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="card mb-6">
        <h2 class="card-title mb-4">Fees</h2>
        <div class="space-y-4">
            <div>
                <label class="label">Enrolment Fee (ZAR)</label>
                <input type="number" name="enrolment_fee" class="input" step="0.01" min="0" placeholder="0.00"
                    value="{{ old('enrolment_fee', $branch->enrolment_fee) }}">
                <p class="text-xs text-gray-400 mt-1">Shown on all public info pages alongside the course fee.</p>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <h2 class="card-title mb-4">Private Lessons</h2>
        <div>
            <label class="label">Private Lesson Fee (ZAR)</label>
            <input type="number" name="private_lesson_fee" class="input" step="0.01" min="0" placeholder="0.00"
                value="{{ old('private_lesson_fee', $branch->private_lesson_fee) }}">
            <p class="text-xs text-gray-400 mt-1">The fee charged per 30-minute private lesson. Applied automatically when a handler books.</p>
        </div>
    </div>

    <div class="card mb-6">
        <h2 class="card-title mb-4">Banking Details</h2>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Bank Name</label>
                    <input type="text" name="bank_name" class="input" placeholder="FNB"
                        value="{{ old('bank_name', $branch->bank_name) }}">
                </div>
                <div>
                    <label class="label">Account Name</label>
                    <input type="text" name="bank_account_name" class="input" placeholder="McKaynine (Pty) Ltd"
                        value="{{ old('bank_account_name', $branch->bank_account_name) }}">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Account Number</label>
                    <input type="text" name="bank_account_number" class="input"
                        value="{{ old('bank_account_number', $branch->bank_account_number) }}">
                </div>
                <div>
                    <label class="label">Branch Code</label>
                    <input type="text" name="bank_branch_code" class="input"
                        value="{{ old('bank_branch_code', $branch->bank_branch_code) }}">
                </div>
            </div>
            <div>
                <label class="label">Payment Reference Note</label>
                <input type="text" name="bank_reference_note" class="input" placeholder="e.g. Use your dog's name as reference"
                    value="{{ old('bank_reference_note', $branch->bank_reference_note) }}">
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <h2 class="card-title mb-4">Legal Details</h2>
        <p class="text-xs text-gray-400 mb-4">Shown in the footer of public class pages as:<br>
            <em>[Branch Entity] T/A [Branch Trading Name] Reg. [Branch Reg No] is a licensed franchise of McKaynine Training pty ltd Reg. [Franchisor Reg No]</em>
        </p>

        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">This Branch</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Registered Entity Name</label>
                    <p class="text-xs text-gray-400 mt-0.5 mb-1">e.g. MTC Honeydew pty ltd</p>
                    <input type="text" name="legal_entity_name" class="input" placeholder="MTC Honeydew pty ltd"
                        value="{{ old('legal_entity_name', $branch->legal_entity_name) }}">
                </div>
                <div>
                    <label class="label">Branch Registration Number</label>
                    <p class="text-xs text-gray-400 mt-0.5 mb-1">Shown after the trading name</p>
                    <input type="text" name="legal_registration_number" class="input" placeholder="2024/128375/07"
                        value="{{ old('legal_registration_number', $branch->legal_registration_number) }}">
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">McKaynine Training pty ltd <span class="normal-case font-normal text-gray-400">(system-wide — same for all branches)</span></h3>
                <div class="max-w-xs">
                    <label class="label">Franchisor Registration Number</label>
                    <p class="text-xs text-gray-400 mt-0.5 mb-1">McKaynine Training pty ltd's reg. number</p>
                    <input type="text" name="franchisor_registration_number" class="input" placeholder="2020/000000/07"
                        value="{{ old('franchisor_registration_number', $franchisorRegNumber) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-6">
        <h2 class="card-title mb-1">InvoicesOnline Integration</h2>
        <p class="text-xs text-gray-400 mb-4">
            API credentials from your InvoicesOnline account — Settings &rarr; API Access tab.
            These are used to automatically create client accounts and issue invoices.
        </p>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">API Username</label>
                    <input type="text" name="io_username" class="input font-mono text-sm"
                        placeholder="Paste from InvoicesOnline"
                        value="{{ old('io_username', $branch->io_username) }}"
                        autocomplete="off">
                </div>
                <div>
                    <label class="label">API Password</label>
                    <input type="password" name="io_password" class="input font-mono text-sm"
                        placeholder="Paste from InvoicesOnline"
                        autocomplete="new-password"
                        value="{{ old('io_password', $branch->io_password ? '••••••••' : '') }}">
                    @if($branch->io_password)
                    <p class="text-xs text-green-600 mt-1">✓ Password saved — leave blank to keep unchanged</p>
                    @endif
                </div>
            </div>
            <div class="max-w-xs">
                <label class="label">Business ID</label>
                <p class="text-xs text-gray-400 mt-0.5 mb-1">Shown on the API Access page for reference</p>
                <input type="text" name="io_business_id" class="input font-mono text-sm"
                    placeholder="e.g. 12345"
                    value="{{ old('io_business_id', $branch->io_business_id) }}">
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">Save Branch Settings</button>
    </div>
</form>
</div>
@endsection
