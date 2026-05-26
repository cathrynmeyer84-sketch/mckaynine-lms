<x-guest-layout>

    @if(session('prefill_email'))
    <div style="text-align:center; margin-bottom:1.25rem;">
        <h2 style="font-family:'Trebuchet MS',sans-serif; font-size:1.15rem; font-weight:700; color:#1A1D2E; margin:0 0 0.3rem;">Create your password</h2>
        <p style="font-size:0.82rem; color:#6b7280; margin:0; line-height:1.6;">Almost there! We'll send you a link to create your McKaynine password.</p>
    </div>
    @else
    <p style="font-size:0.82rem; color:#6b7280; margin:0 0 1.25rem; line-height:1.6;">
        Enter your email address and we'll send you a link to reset your password.
    </p>
    @endif

    @if(session('status'))
    <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', session('prefill_email')) }}"
                   required autofocus
                   placeholder="you@example.com">
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div style="margin-top: 1.25rem;">
            <button type="submit">{{ session('prefill_email') ? 'Send Setup Link' : 'Email Reset Link' }}</button>
        </div>
    </form>

</x-guest-layout>
