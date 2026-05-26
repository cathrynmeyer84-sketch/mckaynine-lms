<x-app-layout :title="$handler->first_name . ' ' . $handler->last_name">
<div class="page-content">

    {{-- Header --}}
    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.handlers.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $handler->first_name }} {{ $handler->last_name }}</h1>
                <p class="page-subtitle">Handler profile</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Handler Info Card --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Handler Information</h2>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Full Name</p>
                        <p class="text-sm font-medium text-gray-900">{{ $handler->first_name }} {{ $handler->last_name }}</p>
                    </div>
                    @if($handler->user)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                        <p class="text-sm text-gray-700">{{ $handler->user->email }}</p>
                    </div>
                    @endif
                    @if($handler->cell_number)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                        <p class="text-sm text-gray-700">{{ $handler->cell_number }}</p>
                    </div>
                    @endif
                    @if($handler->vet_name || $handler->vet_practice)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Vet</p>
                        <p class="text-sm text-gray-700">{{ $handler->vet_name }}@if($handler->vet_practice), {{ $handler->vet_practice }}@endif</p>
                    </div>
                    @endif
                    @if($handler->accountHolder)
                    @php $ah = $handler->accountHolder; @endphp
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Billing Account</p>

                        @if($ah->link_status === 'approved' && $ah->linkedHandler)
                            {{-- Linked to another McKaynine handler, approved --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-gray-700">{{ $ah->linkedHandler->full_name }}</span>
                                <span class="badge badge-active text-xs">Linked ✓</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">McKaynine member · invoices on their account</p>

                        @elseif($ah->link_status === 'pending_approval' && $ah->linkedHandler)
                            {{-- Pending approval --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-gray-700">{{ $ah->linkedHandler->full_name }}</span>
                                <span class="badge badge-pending text-xs">Awaiting approval</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Approval email sent to {{ $ah->email }}
                                @if($ah->link_expires_at) · expires {{ $ah->link_expires_at->format('d M') }}@endif
                            </p>

                        @elseif($ah->link_status === 'rejected' && $ah->linkedHandler)
                            {{-- Declined --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm text-gray-700">{{ $ah->name }}</span>
                                <span class="badge text-xs" style="background:#fee2e2;color:#991b1b;">Declined</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $ah->linkedHandler->full_name }} declined — handler billed separately</p>

                        @else
                            {{-- External contact, no link --}}
                            <p class="text-sm text-gray-700">{{ $ah->name }}</p>
                            @if($ah->email)
                            <p class="text-xs text-gray-400">{{ $ah->email }}</p>
                            @endif
                        @endif

                        @if($handler->invoicesonline_client_id)
                        <p class="text-xs text-gray-400 mt-1 font-mono">IO: {{ $handler->invoicesonline_client_id }}</p>
                        @endif
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Registered</p>
                        <p class="text-sm text-gray-700">{{ $handler->created_at?->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Status</p>
                    @php
                        $statusClass = match($handler->status) {
                            'active' => 'badge-active',
                            'pending' => 'badge-pending',
                            'inactive' => 'badge',
                            default => 'badge'
                        };
                    @endphp
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="badge {{ $statusClass }}">{{ ucfirst($handler->status ?? 'pending') }}</span>
                    </div>
                    <div class="flex gap-2 mt-3 flex-wrap">
                        @foreach(['active', 'inactive', 'pending'] as $s)
                        @if($handler->status !== $s)
                        <form method="POST" action="{{ route('admin.handlers.status', $handler) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $s }}">
                            <button type="submit" class="btn-outline btn-sm">Set {{ ucfirst($s) }}</button>
                        </form>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Assign to Class --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Assign to Class</h2>
                @if($handler->dogs->count())
                <form method="POST" action="{{ route('admin.handlers.assign-class', $handler) }}">
                    @csrf
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Dog</label>
                            <select name="dog_id" class="form-select w-full" required>
                                <option value="">Select dog...</option>
                                @foreach($handler->dogs as $dog)
                                    <option value="{{ $dog->id }}">{{ $dog->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-select w-full" required>
                                <option value="">Select class...</option>
                                @foreach($availableClasses as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary w-full">Assign &amp; Confirm</button>
                    </div>
                </form>
                @else
                <p class="text-sm text-gray-500">This handler has no dogs registered yet.</p>
                @endif
            </div>

        </div>

        {{-- Right column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Dogs --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Dogs ({{ $handler->dogs->count() }})</h2>
                @if($handler->dogs->count())
                <div class="space-y-4">
                    @foreach($handler->dogs as $dog)
                    <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                        {{-- Photo --}}
                        <div class="flex-shrink-0">
                            @if($dog->photo_path)
                                <img src="{{ Storage::url($dog->photo_path) }}" alt="{{ $dog->name }}"
                                    class="w-16 h-16 rounded-xl object-cover">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-stone/30 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-stone" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11H3V9H1.5v2H0v1.5h1.5V15H3v-2.5h1.5V11zm4.75-1.5A2.25 2.25 0 007 11.75v.5A2.25 2.25 0 009.25 14.5h.5A2.25 2.25 0 0012 12.25v-.5A2.25 2.25 0 009.75 9.5h-.5zm5.5 0A2.25 2.25 0 0012.5 11.75v.5A2.25 2.25 0 0014.75 14.5h.5A2.25 2.25 0 0017.5 12.25v-.5A2.25 2.25 0 0015.25 9.5h-.5zM22.5 11H21V9h-1.5v2H18v1.5h1.5V15H21v-2.5h1.5V11z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <a href="{{ route('admin.dogs.show', $dog) }}" class="font-semibold text-navy hover:text-brand underline">{{ $dog->name }}</a>
                                    <p class="text-sm text-gray-500">{{ $dog->breed ?? 'Unknown breed' }}</p>
                                    @if($dog->date_of_birth)
                                        <p class="text-xs text-gray-400">DOB: {{ $dog->date_of_birth->format('d M Y') }} ({{ $dog->date_of_birth->age }} yr{{ $dog->date_of_birth->age !== 1 ? 's' : '' }})</p>
                                    @endif
                                </div>
                            </div>
                            {{-- Vaccination --}}
                            <div class="mt-2 flex flex-wrap gap-2 items-center">
                                @if($dog->vaccination_card_path)
                                    <a href="{{ Storage::url($dog->vaccination_card_path) }}" target="_blank"
                                        class="text-xs text-brand hover:underline flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Vaccination Card
                                    </a>
                                @endif
                                @if($dog->vaccination_expiry)
                                    @php $expiry = \Carbon\Carbon::parse($dog->vaccination_expiry); @endphp
                                    @if($expiry->isPast())
                                        <span class="text-xs text-red-600 font-medium bg-red-50 px-2 py-0.5 rounded-full">Vaccinations EXPIRED {{ $expiry->format('d M Y') }}</span>
                                    @elseif($expiry->diffInDays() < 30)
                                        <span class="text-xs text-amber font-medium bg-amber/10 px-2 py-0.5 rounded-full">Expires soon: {{ $expiry->format('d M Y') }}</span>
                                    @else
                                        <span class="text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">Valid until {{ $expiry->format('d M Y') }}</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11H3V9H1.5v2H0v1.5h1.5V15H3v-2.5h1.5V11z"/></svg>
                    </div>
                    <p>No dogs registered</p>
                </div>
                @endif
            </div>

            {{-- Enrolments --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Enrolments ({{ $handler->enrolments->count() }})</h2>
                @if($handler->enrolments->count())
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Dog</th>
                                <th>Status</th>
                                <th>Enrolled</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($handler->enrolments as $enrolment)
                            <tr>
                                <td class="font-medium text-gray-900">{{ $enrolment->dogClass?->name ?? '—' }}</td>
                                <td class="text-sm text-gray-600">{{ $enrolment->dog?->name ?? '—' }}</td>
                                <td>
                                    @php
                                        $sc = match($enrolment->status) {
                                            'confirmed' => 'badge-confirmed',
                                            'pending' => 'badge-pending',
                                            'completed' => 'badge-completed',
                                            default => 'badge'
                                        };
                                    @endphp
                                    <span class="badge {{ $sc }}">{{ ucfirst($enrolment->status) }}</span>
                                </td>
                                <td class="text-sm text-gray-500">{{ $enrolment->enrolled_at?->format('d M Y') }}</td>
                                <td>
                                    @if($enrolment->status === 'pending')
                                    <div class="flex gap-1">
                                        <form method="POST" action="{{ route('admin.enrolments.confirm', $enrolment) }}">
                                            @csrf
                                            <button type="submit" class="btn-primary btn-sm">Confirm</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.enrolments.reject', $enrolment) }}">
                                            @csrf
                                            <button type="submit" class="btn-danger btn-sm">Reject</button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p>No enrolments yet</p>
                </div>
                @endif
            </div>

            {{-- Billing Actions --}}
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Billing Actions</h2>

                @if(session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="alert alert-error mb-4">{{ session('error') }}</div>
                @endif

                <div x-data="{ tab: 'payment' }" class="space-y-4">
                    {{-- Tab switcher --}}
                    <div class="flex gap-2 border-b border-gray-100 pb-3">
                        <button @click="tab = 'payment'" :class="tab === 'payment' ? 'btn-primary btn-sm' : 'btn-outline btn-sm'">Record Payment</button>
                        <button @click="tab = 'invoice'" :class="tab === 'invoice' ? 'btn-primary btn-sm' : 'btn-outline btn-sm'">Create Invoice</button>
                        <button @click="tab = 'clientid'" :class="tab === 'clientid' ? 'btn-primary btn-sm' : 'btn-outline btn-sm'">IO Client ID</button>
                    </div>

                    {{-- Record Payment --}}
                    <div x-show="tab === 'payment'" x-cloak>
                        <form method="POST" action="{{ route('admin.handlers.billing.payment', $handler) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Amount (R) <span class="text-red-400">*</span></label>
                                    <input type="number" name="amount" step="0.01" min="0.01" class="form-input w-full" placeholder="0.00" required>
                                </div>
                                <div>
                                    <label class="form-label">Date <span class="text-red-400">*</span></label>
                                    <input type="date" name="date" class="form-input w-full" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Method</label>
                                    <select name="method" class="form-select w-full">
                                        <option value="EFT">EFT</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Reference</label>
                                    <input type="text" name="reference" class="form-input w-full" placeholder="e.g. INV-00123">
                                </div>
                            </div>
                            <button type="submit" class="btn-primary w-full">Record in InvoicesOnline</button>
                        </form>
                    </div>

                    {{-- Create Invoice --}}
                    <div x-show="tab === 'invoice'" x-cloak>
                        <form method="POST" action="{{ route('admin.handlers.billing.invoice', $handler) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="form-label">Product Code <span class="text-red-400">*</span></label>
                                <input type="text" name="prod_code" class="form-input w-full font-mono" placeholder="e.g. PUPPY01" required>
                            </div>
                            <div>
                                <label class="form-label">Description <span class="text-red-400">*</span></label>
                                <input type="text" name="description" class="form-input w-full" placeholder="e.g. Puppy School — Buddy" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label">Amount (R) <span class="text-red-400">*</span></label>
                                    <input type="number" name="amount" step="0.01" min="0.01" class="form-input w-full" placeholder="0.00" required>
                                </div>
                                <div>
                                    <label class="form-label">Qty</label>
                                    <input type="number" name="qty" min="1" value="1" class="form-input w-full">
                                </div>
                            </div>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="email" value="1" checked class="rounded">
                                Email invoice to client
                            </label>
                            <button type="submit" class="btn-primary w-full">Create Invoice</button>
                        </form>
                    </div>

                    {{-- IO Client ID --}}
                    <div x-show="tab === 'clientid'" x-cloak>
                        <p class="text-xs text-gray-500 mb-3">
                            The InvoicesOnline client ID is resolved automatically on first invoice. Use this to manually override.
                        </p>
                        <form method="POST" action="{{ route('admin.handlers.billing.client-id', $handler) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="form-label">IO Client ID</label>
                                <input type="text" name="invoicesonline_client_id" class="form-input w-full font-mono"
                                    value="{{ $handler->invoicesonline_client_id }}" placeholder="Leave blank to clear">
                            </div>
                            <button type="submit" class="btn-primary w-full">Save Client ID</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Assessment Requests --}}
            @if($handler->assessmentRequests->count())
            <div class="card">
                <h2 class="font-semibold text-navy mb-4">Assessment Requests</h2>
                <div class="space-y-3">
                    @foreach($handler->assessmentRequests as $assessment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $assessment->dog?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $assessment->created_at?->format('d M Y') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @php
                                $asc = match($assessment->status) {
                                    'pending' => 'badge-pending',
                                    'booked' => 'badge-active',
                                    'completed' => 'badge-completed',
                                    default => 'badge'
                                };
                            @endphp
                            <span class="badge {{ $asc }}">{{ ucfirst($assessment->status) }}</span>
                            <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn-outline btn-sm">View</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</x-app-layout>
