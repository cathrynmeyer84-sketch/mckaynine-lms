@php $user = auth()->user(); $route = request()->route()->getName(); @endphp
@if($user->is_admin)
    <a href="{{ route('admin.dashboard') }}" class="bottom-nav-item {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Home</span>
    </a>
    <a href="{{ route('admin.handlers.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'admin.handlers') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span>Handlers</span>
    </a>
    <a href="{{ route('admin.classes.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'admin.classes') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span>Classes</span>
    </a>
    <a href="{{ route('admin.assessments.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'admin.assessments') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span>Assess</span>
    </a>
    <a href="{{ route('admin.results.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'admin.results') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Results</span>
    </a>
@elseif($user->is_instructor)
    <a href="{{ route('instructor.dashboard') }}" class="bottom-nav-item {{ str_starts_with($route, 'instructor.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Home</span>
    </a>
    <a href="{{ route('instructor.classes.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'instructor.classes') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span>Classes</span>
    </a>
@else
    <a href="{{ route('handler.dashboard') }}" class="bottom-nav-item {{ str_starts_with($route, 'handler.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <span>Home</span>
    </a>
    <a href="{{ route('handler.classes.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'handler.classes') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <span>Classes</span>
    </a>
    <a href="{{ route('handler.dogs.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'handler.dogs') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11H3V9H1.5v2H0v1.5h1.5V15H3v-2.5h1.5V11zm4.75-1.5A2.25 2.25 0 007 11.75v.5A2.25 2.25 0 009.25 14.5h.5A2.25 2.25 0 0012 12.25v-.5A2.25 2.25 0 009.75 9.5h-.5zm5.5 0A2.25 2.25 0 0012.5 11.75v.5A2.25 2.25 0 0014.75 14.5h.5A2.25 2.25 0 0017.5 12.25v-.5A2.25 2.25 0 0015.25 9.5h-.5zM22.5 11H21V9h-1.5v2H18v1.5h1.5V15H21v-2.5h1.5V11z"/></svg>
        <span>Dogs</span>
    </a>
    <a href="{{ route('handler.achievements.index') }}" class="bottom-nav-item {{ str_starts_with($route, 'handler.achievements') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
        <span>Awards</span>
    </a>
    @php $unread = $user->id ? app(\App\Services\MessageService::class)->unreadCount($user->id) : 0; @endphp
    <a href="{{ route('handler.inbox.index') }}" class="bottom-nav-item relative {{ str_starts_with($route, 'handler.inbox') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        @if($unread > 0)<span class="absolute top-1 right-3 bg-amber text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{{ $unread }}</span>@endif
        <span>Inbox</span>
    </a>
@endif
