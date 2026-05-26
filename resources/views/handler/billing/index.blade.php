<x-app-layout title="My Billing">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Billing</h1>
            <p class="page-subtitle">Your invoices and payments</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-error mb-6">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Billing contact info --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Billing Account</h2>
                @if($contact['name'] || $contact['email'])
                <div class="space-y-2">
                    @if($contact['name'])
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Name</p>
                        <p class="text-sm text-gray-800">{{ $contact['name'] }}</p>
                    </div>
                    @endif
                    @if($contact['email'])
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                        <p class="text-sm text-gray-800">{{ $contact['email'] }}</p>
                    </div>
                    @endif
                </div>
                @else
                <p class="text-sm text-gray-500">No billing contact on file. Please contact McKaynine.</p>
                @endif

                @if($ah && $ah->link_status === 'pending_approval')
                <div class="mt-4 p-3 bg-amber/10 border border-amber/30 rounded-xl">
                    <p class="text-xs font-semibold text-amber mb-1">Billing Link Pending</p>
                    <p class="text-sm text-gray-700">An approval request has been sent to {{ $ah->email }}. Your billing will be linked once they confirm.</p>
                </div>
                @endif

                @if(!$hasIo)
                <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-xl">
                    <p class="text-xs text-gray-500">Your invoicing account hasn't been activated yet. Contact McKaynine for assistance.</p>
                </div>
                @endif
            </div>

            {{-- Upload POP --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-1">Upload Proof of Payment</h2>
                <p class="text-xs text-gray-500 mb-4">Attach a bank confirmation or EFT screenshot for a specific invoice.</p>

                <form method="POST" action="{{ route('handler.billing.pop.upload') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Invoice Reference <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="text" name="invoice_reference" class="form-input w-full" placeholder="e.g. INV-00123">
                    </div>
                    <div>
                        <label class="form-label">Amount Paid <span class="text-gray-400 font-normal">(optional)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">R</span>
                            <input type="number" name="amount" step="0.01" min="0" class="form-input w-full pl-7" placeholder="0.00">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">File <span class="text-red-400">*</span></label>
                        <input type="file" name="pop_file" accept=".jpg,.jpeg,.png,.gif,.pdf" class="form-input w-full" required>
                        <p class="text-xs text-gray-400 mt-1">JPEG, PNG, GIF or PDF · max 10 MB</p>
                    </div>
                    <button type="submit" class="btn-primary w-full">Upload</button>
                </form>
            </div>

            {{-- POP History --}}
            @if($pops->count())
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Uploaded POPs</h2>
                <div class="space-y-2">
                    @foreach($pops as $pop)
                    <div class="flex items-start justify-between gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="flex-1 min-w-0">
                            @if($pop->invoice_reference)
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $pop->invoice_reference }}</p>
                            @else
                            <p class="text-sm text-gray-400 italic">No reference</p>
                            @endif
                            @if($pop->amount)
                            <p class="text-xs text-gray-600">R{{ number_format($pop->amount, 2) }}</p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $pop->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="shrink-0">
                            @if($pop->is_reviewed)
                            <span class="badge badge-active text-xs">Reviewed</span>
                            @else
                            <span class="badge badge-pending text-xs">Pending</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right: Invoices --}}
        <div class="lg:col-span-2">
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Invoices &amp; Statements</h2>

                @if($invoices)
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                            <tr>
                                <td class="font-mono text-sm text-gray-700">{{ $inv['document_nr'] ?? '—' }}</td>
                                <td class="text-sm text-gray-600">
                                    {{ isset($inv['document_date']) ? \Carbon\Carbon::parse($inv['document_date'])->format('d M Y') : '—' }}
                                </td>
                                <td class="text-sm text-gray-700 max-w-xs truncate">
                                    {{ $inv['description'] ?? $inv['notes'] ?? '—' }}
                                </td>
                                <td class="text-sm text-gray-900 text-right font-medium">
                                    R{{ isset($inv['total']) ? number_format((float)$inv['total'], 2) : '0.00' }}
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($inv['status'] ?? '');
                                        $badgeClass = match(true) {
                                            str_contains($status, 'paid') => 'badge-active',
                                            str_contains($status, 'overdue') => 'badge text-red-600 bg-red-50',
                                            str_contains($status, 'partial') => 'badge-pending',
                                            default => 'badge-pending'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-xs">{{ $inv['status'] ?? 'Outstanding' }}</span>
                                </td>
                                <td>
                                    @if(!empty($inv['url']))
                                    <a href="{{ $inv['url'] }}" target="_blank" class="btn-outline btn-sm">View</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-sm text-gray-400 py-6">No invoices found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <p>No invoices available yet.</p>
                    <p class="text-xs text-gray-400 mt-1">Invoices will appear here once your billing account is set up.</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
</x-app-layout>
