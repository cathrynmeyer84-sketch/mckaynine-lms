<x-app-layout :title="'Vet Clearance Certificate'">
<div class="page-content max-w-2xl mx-auto">

    <div class="page-header">
        <div>
            <h1 class="page-title">Vet Clearance Certificate</h1>
            <p class="page-subtitle">{{ $enrolment->dog?->name }} · {{ $enrolment->dogClass?->name }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if($enrolment->vet_clearance_path)
    <div class="card bg-green-50 border border-green-100 mb-6">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <p class="font-semibold text-green-700">Certificate uploaded</p>
                <p class="text-sm text-green-600">We've received your vet clearance certificate and are reviewing it. We'll be in touch shortly.</p>
            </div>
        </div>
        <div class="mt-3 pl-9">
            <a href="{{ Storage::url($enrolment->vet_clearance_path) }}" target="_blank"
                class="text-xs text-brand underline">View uploaded certificate →</a>
        </div>
    </div>
    @endif

    <div class="card mb-6">
        <h2 class="font-semibold text-navy mb-3">Step 1 — Download the form</h2>
        <p class="text-sm text-gray-600 mb-4">Download our vet clearance form and take it to your vet to complete and sign.</p>
        <a href="{{ route('vet-clearance.pdf') }}" class="btn-outline inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download Vet Clearance Form (PDF)
        </a>
    </div>

    <div class="card">
        <h2 class="font-semibold text-navy mb-3">Step 2 — Upload the completed certificate</h2>
        <p class="text-sm text-gray-600 mb-4">Once your vet has completed and signed the form, upload it here.</p>

        <form method="POST" action="{{ route('handler.vet-clearance.upload.store', $enrolment) }}"
            enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <div>
                <label class="form-label">Completed certificate (PDF or photo)</label>
                <input type="file" name="vet_clearance" accept=".pdf,.jpg,.jpeg,.png"
                    class="form-input w-full" required>
                <p class="text-xs text-gray-400 mt-1">PDF, JPG or PNG · max 5MB</p>
            </div>

            <button type="submit" class="btn-primary">Upload Certificate</button>
        </form>
    </div>

</div>
</x-app-layout>
