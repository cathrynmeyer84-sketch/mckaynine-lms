<x-guest-layout>

    @if(session('status'))
    <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', session('prefill_email')) }}"
                   required autofocus autocomplete="username"
                   placeholder="you@example.com">
            @error('email')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-actions">
            <label style="display:flex; align-items:center; gap:7px; font-size:0.8rem; color:#6b7280; cursor:pointer;">
                <input type="checkbox" name="remember" style="margin:0;">
                Remember me
            </label>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <div style="margin-top: 1.25rem;">
            <button type="submit">Sign In</button>
        </div>
    </form>

</x-guest-layout>
