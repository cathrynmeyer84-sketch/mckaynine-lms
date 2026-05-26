<x-app-layout title="Proof of Payment Queue">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Proof of Payment Queue</h1>
            <p class="page-subtitle">Handler-uploaded payment confirmations awaiting review</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if($pops->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p>All caught up — no pending POPs.</p>
        </div>
    </div>
    @else
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Handler</th>
                        <th>Invoice Ref</th>
                        <th>Amount</th>
                        <th>Uploaded</th>
                        <th>File</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pops as $pop)
                    <tr>
                        <td>
                            <a href="{{ route('admin.handlers.show', $pop->handler) }}" class="font-medium text-navy hover:underline">
                                {{ $pop->handler->full_name }}
                            </a>
                        </td>
                        <td class="font-mono text-sm text-gray-700">{{ $pop->invoice_reference ?: '—' }}</td>
                        <td class="text-sm text-gray-900">
                            @if($pop->amount)
                                R{{ number_format($pop->amount, 2) }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">{{ $pop->created_at->format('d M Y, g:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.billing.pops.download', $pop) }}" class="btn-outline btn-sm">
                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download
                            </a>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.billing.pops.review', $pop) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Mark Reviewed</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
