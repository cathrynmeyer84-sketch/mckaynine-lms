<x-app-layout :title="'My Fees'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">My Fees</h1>
            <p class="page-subtitle">Your released fee statements</p>
        </div>
    </div>

    @if($statements->isEmpty())
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p>No fee statements yet</p>
            <p class="text-sm text-gray-400 mt-1">Your fee statements will appear here once they have been released by admin.</p>
        </div>
    </div>
    @else
    <div class="space-y-4">
        @foreach($statements as $statement)
        <div class="card">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="font-semibold text-navy text-base">{{ $statement->period_label }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        @if($statement->is_paid)
                            <span class="badge badge-active">Paid {{ $statement->paid_at->format('d M Y') }}</span>
                        @else
                            <span class="badge badge-pending">Pending payment</span>
                        @endif
                        <span class="badge">{{ ucfirst($statement->instructor->payment_frequency ?? 'termly') }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total</p>
                    <p class="text-xl font-bold text-navy">R {{ number_format($statement->total, 2) }}</p>
                    <a href="{{ route('instructor.fees.show', $statement) }}" class="text-xs text-brand hover:underline mt-1 inline-block">
                        View Breakdown
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
</x-app-layout>
