{{-- $stmt, $instructorId, $termKey --}}
<div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
    <div class="flex items-center gap-2">
        @if($stmt?->is_paid)
            <span class="badge badge-active">Paid {{ $stmt->paid_at->format('d M Y') }}</span>
        @elseif($stmt?->is_released)
            <span class="badge badge-pending">Released – awaiting payment</span>
        @else
            <span class="text-xs text-gray-400">Not yet released</span>
        @endif
    </div>
    <div class="flex items-center gap-2">
        {{-- Release / Re-release --}}
        <form method="POST" action="{{ route('admin.fees.statements.release') }}">
            @csrf
            <input type="hidden" name="instructor_id" value="{{ $instructorId }}">
            <input type="hidden" name="term" value="{{ $termKey }}">
            <button type="submit" class="btn btn-outline text-xs py-1.5 px-3">
                {{ $stmt?->is_released ? 'Re-release' : 'Release to Instructor' }}
            </button>
        </form>
        {{-- Mark as paid --}}
        @if($stmt && !$stmt->is_paid)
        <form method="POST" action="{{ route('admin.fees.statements.pay', $stmt) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary text-xs py-1.5 px-3">Mark as Paid</button>
        </form>
        @elseif($stmt?->is_paid)
        <form method="POST" action="{{ route('admin.fees.statements.unpay', $stmt) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline text-xs py-1.5 px-3 text-gray-500">Undo Payment</button>
        </form>
        @endif
    </div>
</div>
