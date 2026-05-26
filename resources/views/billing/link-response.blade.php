<x-guest-layout>
    <div style="text-align:center; padding: 8px 0;">

        @if($status === 'approved')
        <div style="width:56px;height:56px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg style="width:28px;height:28px;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 style="font-family:'Trebuchet MS',sans-serif;font-size:1.1rem;font-weight:700;color:#1A1D2E;margin:0 0 10px;">Billing link approved</h2>

        @elseif($status === 'declined')
        <div style="width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg style="width:28px;height:28px;color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <h2 style="font-family:'Trebuchet MS',sans-serif;font-size:1.1rem;font-weight:700;color:#1A1D2E;margin:0 0 10px;">Billing link declined</h2>

        @else
        <div style="width:56px;height:56px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <svg style="width:28px;height:28px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 style="font-family:'Trebuchet MS',sans-serif;font-size:1.1rem;font-weight:700;color:#1A1D2E;margin:0 0 10px;">Link unavailable</h2>
        @endif

        <p style="font-size:0.875rem;color:#6b7280;line-height:1.6;margin:0;">{{ $message }}</p>

        <div style="margin-top:28px;">
            <a href="{{ url('/') }}"
               style="display:inline-block;padding:10px 24px;background:#1A1D2E;color:#fff;border-radius:10px;font-size:0.875rem;font-weight:600;text-decoration:none;">
                Go to McKaynine
            </a>
        </div>
    </div>
</x-guest-layout>
