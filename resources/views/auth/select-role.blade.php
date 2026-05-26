<x-guest-layout>

    <style>
        .role-btn {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            width: 100%;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 14px;
            padding: 0.9rem 1.1rem;
            cursor: pointer;
            text-align: left;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            font-family: 'Open Sans', sans-serif;
            text-transform: none;
            letter-spacing: 0;
            color: #1A1D2E;
        }
        .role-btn:hover {
            border-color: #3569BF;
            background: #f0f5ff;
            box-shadow: 0 4px 14px rgba(53,105,191,0.12);
            transform: none;
        }
        .role-btn-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .role-btn-icon.admin      { background: #eef2ff; }
        .role-btn-icon.instructor { background: #f0fdf4; }
        .role-btn-icon.handler    { background: #fdf4ff; }
        .role-btn-label { font-size: 0.88rem; font-weight: 700; color: #1A1D2E; }
        .role-btn-sub   { font-size: 0.75rem; color: #9ca3af; margin-top: 1px; }
    </style>

    <p style="text-align:center; font-size:0.82rem; color:#6b7280; margin-bottom:1.5rem; line-height:1.5;">
        Welcome back, <strong style="color:#1A1D2E;">{{ auth()->user()->display_name }}</strong>.<br>
        Which portal would you like to open?
    </p>

    <div style="display:flex; flex-direction:column; gap:0.65rem;">

        @if(in_array('admin', $roles))
        <form method="POST" action="{{ route('auth.select-role.post') }}">
            @csrf
            <input type="hidden" name="role" value="admin">
            <button type="submit" class="role-btn">
                <div class="role-btn-icon admin">
                    <svg width="18" height="18" fill="none" stroke="#3569BF" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="role-btn-label">Admin Portal</div>
                    <div class="role-btn-sub">Manage classes, enrolments &amp; fees</div>
                </div>
                <svg style="margin-left:auto; flex-shrink:0; color:#d1d5db;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </form>
        @endif

        @if(in_array('instructor', $roles))
        <form method="POST" action="{{ route('auth.select-role.post') }}">
            @csrf
            <input type="hidden" name="role" value="instructor">
            <button type="submit" class="role-btn">
                <div class="role-btn-icon instructor">
                    <svg width="18" height="18" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <div>
                    <div class="role-btn-label">Instructor Portal</div>
                    <div class="role-btn-sub">View your classes &amp; fee statements</div>
                </div>
                <svg style="margin-left:auto; flex-shrink:0; color:#d1d5db;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </form>
        @endif

        @if(in_array('handler', $roles))
        <form method="POST" action="{{ route('auth.select-role.post') }}">
            @csrf
            <input type="hidden" name="role" value="handler">
            <button type="submit" class="role-btn">
                <div class="role-btn-icon handler">
                    <svg width="18" height="18" fill="none" stroke="#9333ea" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="role-btn-label">Handler Portal</div>
                    <div class="role-btn-sub">Track your dogs &amp; progress</div>
                </div>
                <svg style="margin-left:auto; flex-shrink:0; color:#d1d5db;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </form>
        @endif

    </div>

</x-guest-layout>
