<x-guest-layout>

    <p style="font-size:0.82rem; color:#6b7280; margin:0 0 1.25rem; line-height:1.6;">
        This is a secure area. Please confirm your password before continuing.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="auth-field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div style="margin-top: 1.25rem;">
            <button type="submit">Confirm</button>
        </div>
    </form>

</x-guest-layout>
