@php
    $user = auth()->user();
    $currentRoute = request()->route()->getName();
@endphp

@if($user->is_admin)
    {{-- ── Admin section ────────────────────────────────── --}}
    <div class="flex items-center justify-between px-3 pt-2 pb-1">
        <p class="text-white/30 text-xs font-semibold uppercase tracking-wider">Admin</p>
        @if($user->is_super_admin)
        <span class="text-[10px] font-semibold bg-amber/20 text-amber rounded px-1.5 py-0.5 leading-none tracking-wide">SUPER</span>
        @endif
    </div>

    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.dashboard') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>
    <a href="{{ route('admin.assessments.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.assessments') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        Assessments
    </a>
    <a href="{{ route('admin.enrolments.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.enrolments') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
        Enrolments
    </a>
    <a href="{{ route('admin.handlers.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.handlers') || str_starts_with($currentRoute, 'admin.dogs') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Handlers
    </a>
    <a href="{{ route('admin.classes.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.classes') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Classes
    </a>
    <a href="{{ route('admin.private-lessons.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.private-lessons') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Private Lessons
    </a>
    <a href="{{ route('admin.results.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.results') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Results
    </a>
    @php $adminUnread = app(\App\Services\MessageService::class)->unreadCount(auth()->id()); @endphp
    <a href="{{ route('admin.inbox.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.inbox') && !str_starts_with($currentRoute, 'admin.inbox.templates') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Inbox
        @if($adminUnread > 0)<span class="ml-auto bg-brand text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $adminUnread }}</span>@endif
    </a>

    {{-- ── Super Admin section ──────────────────────────── --}}
    @if($user->is_super_admin)
    <div class="my-3 border-t border-white/10"></div>
    <p class="px-3 pt-1 pb-1 text-white/30 text-xs font-semibold uppercase tracking-wider flex items-center gap-1.5">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        Super Admin
    </p>
    <a href="{{ route('admin.branch-settings.edit') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.branch-settings') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Branch Settings
    </a>
    <a href="{{ route('admin.class-types.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.class-types') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Class Types
    </a>
    <a href="{{ route('admin.instructors.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.instructors') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Instructors
    </a>
    <a href="{{ route('admin.calendar.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.calendar') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Calendar
    </a>
    <a href="{{ route('admin.email-templates.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.email-templates') || str_starts_with($currentRoute, 'admin.inbox.templates') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Email Templates
    </a>
    <a href="{{ route('admin.resources.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.resources') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        Resources
    </a>
    @php $pendingPops = \App\Models\BillingPop::where('is_reviewed', false)->count(); @endphp
    <a href="{{ route('admin.billing.pops') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.billing') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Billing
        @if($pendingPops > 0)<span class="ml-auto bg-amber text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $pendingPops }}</span>@endif
    </a>
    <a href="{{ route('admin.fees.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'admin.fees') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Instructor Fees
    </a>
    @endif {{-- end super admin --}}

@endif {{-- end is_admin --}}

@if($user->is_instructor)
    @if($user->is_admin)<div class="my-3 border-t border-white/10"></div>@endif
    <p class="px-3 pt-2 pb-1 text-white/30 text-xs font-semibold uppercase tracking-wider">Instructor</p>
    <a href="{{ route('instructor.dashboard') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'instructor.dashboard') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        My Dashboard
    </a>
    <a href="{{ route('instructor.classes.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'instructor.classes') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        My Classes
    </a>
    @php $instructorUnread = app(\App\Services\MessageService::class)->unreadCount($user->id); @endphp
    <a href="{{ route('instructor.inbox.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'instructor.inbox') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        Inbox
        @if($instructorUnread > 0)<span class="ml-auto bg-brand text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $instructorUnread }}</span>@endif
    </a>
    @php
    $instructor = $user->instructor ?? null;
    $pendingLessons = $instructor
        ? \App\Models\PrivateLesson::where('instructor_id', $instructor->id)->where('status', 'pending')->count()
        : 0;
    @endphp
    <a href="{{ route('instructor.private-lessons.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'instructor.private-lessons') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Private Lessons
        @if($pendingLessons > 0)
        <span class="ml-auto bg-amber text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $pendingLessons }}</span>
        @endif
    </a>
    <a href="{{ route('instructor.fees.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'instructor.fees') ? 'active' : '' }}">
        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Fees
    </a>
    <a href="{{ route('instructor.profile.edit') }}" class="sidebar-link {{ $currentRoute === 'instructor.profile.edit' ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        My Profile
    </a>
@endif

@if($user->is_handler)
    @if($user->is_admin || $user->is_instructor)<div class="my-3 border-t border-white/10"></div>@endif
    <p class="px-3 pt-2 pb-1 text-white/30 text-xs font-semibold uppercase tracking-wider">My Learning</p>
    <a href="{{ route('handler.dashboard') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.dashboard') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        My Dashboard
    </a>
    <a href="{{ route('handler.classes.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.classes') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        My Classes
    </a>
    <a href="{{ route('handler.dogs.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.dogs') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11H3V9H1.5v2H0v1.5h1.5V15H3v-2.5h1.5V11zm4.75-1.5A2.25 2.25 0 007 11.75v.5A2.25 2.25 0 009.25 14.5h.5A2.25 2.25 0 0012 12.25v-.5A2.25 2.25 0 009.75 9.5h-.5zm5.5 0A2.25 2.25 0 0012.5 11.75v.5A2.25 2.25 0 0014.75 14.5h.5A2.25 2.25 0 0017.5 12.25v-.5A2.25 2.25 0 0015.25 9.5h-.5zM22.5 11H21V9h-1.5v2H18v1.5h1.5V15H21v-2.5h1.5V11z"/></svg>
        My Dogs
    </a>
    @php $newAchievements = auth()->user()->newAchievementsCount(); @endphp
    <a href="{{ route('handler.achievements.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.achievements') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
        Achievements
        @if($newAchievements > 0)
        <span class="ml-auto bg-amber text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">{{ $newAchievements }}</span>
        @endif
    </a>
    @php $unreadCount = $user->id ? app(\App\Services\MessageService::class)->unreadCount($user->id) : 0; @endphp
    <a href="{{ route('handler.inbox.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.inbox') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        Inbox
        @if($unreadCount > 0)
        <span class="ml-auto bg-amber text-white text-xs rounded-full px-1.5 py-0.5 min-w-[20px] text-center">{{ $unreadCount }}</span>
        @endif
    </a>
    <a href="{{ route('handler.resources.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.resources') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
        Resources
    </a>
    <a href="{{ route('enrol.start') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'enrol') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Enrol / New Dog
    </a>
    <a href="{{ route('handler.private-lessons.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.private-lessons') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Private Lessons
    </a>
    <a href="{{ route('handler.billing.index') }}" class="sidebar-link {{ str_starts_with($currentRoute, 'handler.billing') ? 'active' : '' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Billing
    </a>
@endif
