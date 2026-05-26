<x-guest-layout>
    @php $isSetup = request()->boolean('setup'); @endphp

    <div style="text-align:center; margin-bottom:1.5rem;">
        <h2 style="font-family:'Trebuchet MS',sans-serif; font-size:1.15rem; font-weight:700; color:#1A1D2E; margin:0 0 0.3rem;">
            {{ $isSetup ? 'Set up your account' : 'Reset your password' }}
        </h2>
        <p style="font-size:0.8rem; color:#9ca3af; margin:0;">
            {{ $isSetup ? 'Choose a password to activate your McKaynine account.' : 'Enter your new password below.' }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', $request->email) }}"
                   required autofocus autocomplete="username">
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field">
            <label for="password">{{ $isSetup ? 'Choose a password' : 'New Password' }}</label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password"
                   placeholder="••••••••">
            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   placeholder="••••••••">
            @error('password_confirmation')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div style="margin-top: 1.25rem;">
            <button type="submit">
                {{ $isSetup ? 'Activate Account' : 'Reset Password' }}
            </button>
        </div>
    </form>

</x-guest-layout>
