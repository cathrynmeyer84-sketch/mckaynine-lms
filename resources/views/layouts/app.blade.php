<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#001d6d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">

    <title>{{ config('app.name', 'McKaynine LMS') }} @isset($title) — {{ $title }} @endisset @hasSection('title') — @yield('title') @endif</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        const VAPID_PUBLIC_KEY = '{{ config('webpush.vapid.public_key') }}';

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(reg => {
                    window._swReg = reg;
                });
            });
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const raw = window.atob(base64);
            return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
        }

        async function subscribeToPush() {
            if (!('PushManager' in window)) return;
            try {
                const reg = window._swReg || await navigator.serviceWorker.ready;
                const existing = await reg.pushManager.getSubscription();
                if (existing) return existing;

                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
                });

                const key  = sub.getKey('p256dh');
                const auth = sub.getKey('auth');

                await fetch('{{ route('push.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({
                        endpoint:         sub.endpoint,
                        public_key:       key  ? btoa(String.fromCharCode(...new Uint8Array(key)))  : null,
                        auth_token:       auth ? btoa(String.fromCharCode(...new Uint8Array(auth))) : null,
                        content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
                    }),
                });
                return sub;
            } catch (err) {
                console.warn('Push subscription failed:', err);
            }
        }

        // Auto-subscribe if already granted
        @auth
        document.addEventListener('DOMContentLoaded', () => {
            if (Notification.permission === 'granted') {
                navigator.serviceWorker.ready.then(() => subscribeToPush());
            }
        });
        @endauth
    </script>
</head>
<body class="h-full bg-brand-beige font-sans antialiased" x-data="{ sidebarOpen: false, mobileMenuOpen: false }">

    {{-- Desktop sidebar + main layout --}}
    <div class="flex h-full">

        {{-- Desktop Sidebar --}}
        @auth
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-navy z-40">
            {{-- Logo --}}
            <div class="flex items-center h-16 px-6 border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                    </div>
                    <span class="text-white font-semibold text-sm">McKaynine LMS</span>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                @include('layouts.partials.sidebar-nav')
            </nav>

            {{-- User section --}}
            <div class="p-4 border-t border-white/10">
                @php
                    $portalCount = collect([auth()->user()->is_admin, auth()->user()->is_instructor, auth()->user()->is_handler])->filter()->count();
                @endphp
                @if($portalCount > 1)
                <a href="{{ route('auth.select-role') }}"
                   class="flex items-center gap-2 w-full mb-3 px-2 py-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors text-xs font-medium">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Switch portal
                </a>
                @endif
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-brand rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-white/50 text-xs truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white/50 hover:text-white transition-colors" title="Sign out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        @endauth

        {{-- Main content area --}}
        <div class="flex-1 flex flex-col {{ auth()->check() ? 'lg:pl-64' : '' }}">

            {{-- Mobile top bar --}}
            <header class="lg:hidden sticky top-0 z-30 bg-navy flex items-center justify-between px-4 h-14 shadow-md">
                <a href="{{ auth()->check() ? route('dashboard') : route('enrol.start') }}" class="flex items-center gap-2">
                    <div class="w-7 h-7 bg-amber rounded flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                    </div>
                    <span class="text-white font-semibold text-sm">McKaynine</span>
                </a>
                <div class="flex items-center gap-3">
                    @auth
                    {{-- Notifications --}}
                    <a href="{{ route('notifications.index') }}" class="relative text-white/70 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(auth()->user()->unreadNotificationsCount() > 0)
                            <span class="absolute -top-1 -right-1 bg-amber text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{{ auth()->user()->unreadNotificationsCount() }}</span>
                        @endif
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white/70 hover:text-white">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    @endauth
                </div>
            </header>

            {{-- Mobile menu overlay --}}
            @auth
            <div x-cloak x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

            {{-- Mobile menu drawer --}}
            <div x-cloak x-show="mobileMenuOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-72 bg-navy z-50 lg:hidden flex flex-col">
                <div class="flex items-center h-14 px-4 border-b border-white/10">
                    <span class="text-white font-semibold">McKaynine LMS</span>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    @include('layouts.partials.sidebar-nav')
                </nav>
                <div class="p-4 border-t border-white/10 space-y-2">
                    @if(isset($portalCount) ? $portalCount > 1 : collect([auth()->user()->is_admin, auth()->user()->is_instructor, auth()->user()->is_handler])->filter()->count() > 1)
                    <a href="{{ route('auth.select-role') }}"
                       class="flex items-center gap-2 text-white/70 hover:text-white text-sm w-full py-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Switch portal
                    </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-white/70 hover:text-white text-sm w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
            @endauth

            {{-- Desktop top bar --}}
            <div class="hidden lg:flex items-center justify-between px-8 h-16 bg-white border-b border-gray-200">
                <div>
                    @isset($title)
                        <h1 class="text-xl font-semibold text-navy">{{ $title }}</h1>
                    @endisset
                    @hasSection('title')
                        <h1 class="text-xl font-semibold text-navy">@yield('title')</h1>
                    @endif
                </div>
                @auth
                <div class="flex items-center gap-4">
                    <a href="{{ route('notifications.index') }}" class="relative text-gray-500 hover:text-navy transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(auth()->user()->unreadNotificationsCount() > 0)
                            <span class="absolute -top-1 -right-1 bg-amber text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{{ auth()->user()->unreadNotificationsCount() }}</span>
                        @endif
                    </a>
                    @if(collect([auth()->user()->is_admin, auth()->user()->is_instructor, auth()->user()->is_handler])->filter()->count() > 1)
                    <a href="{{ route('auth.select-role') }}"
                       class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-navy font-medium transition-colors border border-gray-200 hover:border-navy rounded-lg px-2.5 py-1.5"
                       title="Switch portal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Switch portal
                    </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-navy font-medium transition-colors">{{ auth()->user()->name }}</a>
                </div>
                @endauth
            </div>

            {{-- Page content --}}
            <main class="flex-1 overflow-y-auto pb-20 lg:pb-8">
                {{-- Flash toast --}}
                @if(session('success') || session('error') || session('info'))
                    @php
                        $toastMsg   = session('success') ?? session('error') ?? session('info');
                        $toastColor = session('error') ? 'bg-red-500' : (session('info') ? 'bg-brand' : 'bg-green-500');
                    @endphp
                    <div id="flashToast"
                         class="fixed top-5 right-5 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg text-white text-sm font-medium {{ $toastColor }} transition-all duration-500">
                        <span>{{ $toastMsg }}</span>
                        <button onclick="document.getElementById('flashToast').remove()" class="opacity-70 hover:opacity-100 text-lg leading-none">&times;</button>
                    </div>
                    <script>
                        setTimeout(function () {
                            const t = document.getElementById('flashToast');
                            if (t) { t.style.opacity = '0'; setTimeout(() => t.remove(), 500); }
                        }, 4000);
                    </script>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            {{-- Mobile bottom navigation --}}
            @auth
            <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-30">
                <div class="flex">
                    @include('layouts.partials.bottom-nav')
                </div>
            </nav>
            @endauth
        </div>
    </div>
</body>
</html>
