@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Hello, {{ $handler?->first_name ?? auth()->user()->name }} 👋</h1>
        <p class="page-subtitle">Welcome to McKaynine Dog School</p>
    </div>
</div>

<div class="page-content">

    {{-- Push notification permission prompt --}}
    @auth
    <div id="pushPrompt" class="card border-2 border-amber/30 bg-amber/5 mb-6 hidden">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-amber/20 rounded-full flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-navy text-sm">Stay in the loop</p>
                <p class="text-sm text-gray-600">Turn on notifications to get updates about your classes and enrolment.</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button onclick="enableNotifications()" class="btn btn-primary btn-sm">Enable</button>
                <button onclick="document.getElementById('pushPrompt').remove()" class="text-gray-400 hover:text-gray-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if ('Notification' in window && Notification.permission === 'default') {
                document.getElementById('pushPrompt').classList.remove('hidden');
            }
        });
        async function enableNotifications() {
            const permission = await Notification.requestPermission();
            document.getElementById('pushPrompt').remove();
            if (permission === 'granted') await subscribeToPush();
        }
    </script>
    @endauth

    {{-- Pending enrolment prompt --}}
    @if($pendingEnrolment)
    <div class="card border-2 border-brand/30 bg-brand/5 mb-6 flex items-center gap-4">
        <div class="w-10 h-10 bg-brand/10 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-navy text-sm">Your assessment is complete!</p>
            <p class="text-sm text-gray-600">Complete your enrolment to secure your place in class.</p>
        </div>
        <a href="{{ route('enrol.complete') }}" class="btn btn-primary shrink-0">Complete Enrolment →</a>
    </div>
    @endif

    {{-- Recommended classes for pending assessment enrolments --}}
    @if($recommendedClassTypes->isNotEmpty())
    <div class="card mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl bg-amber/15 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-navy text-sm">Your Recommended Classes</p>
                <p class="text-xs text-gray-400">Based on your assessment — browse the options below. We'll be in touch to confirm your placement.</p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($recommendedClassTypes as $ct)
            <a href="{{ route('class-info.show', $ct->slug) }}"
               class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-brand/5 border border-gray-100 hover:border-brand/20 transition-colors group">
                <div>
                    <p class="font-semibold text-navy text-sm">{{ $ct->name }}</p>
                    @if($ct->tagline)
                    <p class="text-xs text-gray-400 mt-0.5">{{ $ct->tagline }}</p>
                    @endif
                </div>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-brand transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick links --}}
    <section class="mb-8">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

            <a href="{{ route('handler.dogs.index') }}" class="card card-hover flex flex-col items-center justify-center py-6 gap-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-amber/15 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3 12a8.96 8.96 0 0 0 .284 2.253"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-navy text-sm">My Dogs</p>
                    @if($dogs->count())
                    <p class="text-xs text-gray-400 mt-0.5">{{ $dogs->count() }} {{ Str::plural('dog', $dogs->count()) }}</p>
                    @else
                    <p class="text-xs text-gray-400 mt-0.5">No dogs yet</p>
                    @endif
                </div>
            </a>

            <a href="{{ route('handler.classes.index') }}" class="card card-hover flex flex-col items-center justify-center py-6 gap-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-brand/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-navy text-sm">My Classes</p>
                    @if($enrolments->count())
                    <p class="text-xs text-gray-400 mt-0.5">{{ $enrolments->count() }} active</p>
                    @else
                    <p class="text-xs text-gray-400 mt-0.5">None yet</p>
                    @endif
                </div>
            </a>

            <a href="{{ route('handler.achievements.index') }}" class="card card-hover flex flex-col items-center justify-center py-6 gap-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-yellow-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-navy text-sm">Achievements</p>
                    <p class="text-xs text-gray-400 mt-0.5">Grades &amp; results</p>
                </div>
            </a>

            <a href="{{ route('handler.inbox.index') }}" class="card card-hover flex flex-col items-center justify-center py-6 gap-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-navy text-sm">Inbox</p>
                    @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
                    @if($unread)
                    <p class="text-xs text-brand font-medium mt-0.5">{{ $unread }} unread</p>
                    @else
                    <p class="text-xs text-gray-400 mt-0.5">Messages</p>
                    @endif
                </div>
            </a>

            <a href="{{ route('handler.resources.index') }}" class="card card-hover flex flex-col items-center justify-center py-6 gap-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-navy text-sm">Resources</p>
                    <p class="text-xs text-gray-400 mt-0.5">Guides &amp; handouts</p>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="card card-hover flex flex-col items-center justify-center py-6 gap-3 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <div>
                    <p class="font-semibold text-navy text-sm">My Account</p>
                    <p class="text-xs text-gray-400 mt-0.5">Profile &amp; billing</p>
                </div>
            </a>

        </div>
    </section>

    {{-- Active classes summary --}}
    @if($enrolments->isNotEmpty())
    <section class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-navy">Active Classes</h2>
            <a href="{{ route('handler.classes.index') }}" class="text-sm text-brand font-medium">View all</a>
        </div>
        <div class="grid gap-3 md:grid-cols-2">
            @foreach($enrolments as $enrolment)
            <a href="{{ route('handler.classes.show', $enrolment) }}" class="card card-hover flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-brand/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-navy text-sm truncate">{{ $enrolment->dogClass?->name ?? 'Awaiting class assignment' }}</p>
                    <p class="text-xs text-gray-500">{{ $enrolment->dog?->name }}@if($enrolment->dogClass?->start_date) &middot; Starts {{ $enrolment->dogClass->start_date->format('d M Y') }}@endif</p>
                </div>
                <span class="badge badge-{{ $enrolment->status }} shrink-0">{{ $enrolment->status_label }}</span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Dogs summary --}}
    @if($dogs->isNotEmpty())
    <section class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-navy">My Dogs</h2>
            <a href="{{ route('handler.dogs.index') }}" class="text-sm text-brand font-medium">View all</a>
        </div>
        <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            @foreach($dogs as $dog)
            <a href="{{ route('handler.dogs.edit', $dog) }}" class="card card-hover flex items-center gap-4">
                @if($dog->photo_path)
                <img src="{{ Storage::url($dog->photo_path) }}" alt="{{ $dog->name }}" class="w-12 h-12 rounded-full object-cover shrink-0">
                @else
                <div class="w-12 h-12 rounded-full bg-amber/15 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11.5A6.5 6.5 0 0 1 11 5h2a6.5 6.5 0 0 1 6.5 6.5A4.5 4.5 0 0 1 15 16v1a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1v-1a4.5 4.5 0 0 1-4.5-4.5Z"/></svg>
                </div>
                @endif
                <div class="min-w-0">
                    <p class="font-semibold text-navy text-sm">{{ $dog->name }}</p>
                    <p class="text-xs text-gray-500">{{ $dog->breed ?? 'Mixed breed' }}{{ $dog->age_in_months ? ' · ' . $dog->age_in_months . ' months' : '' }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
