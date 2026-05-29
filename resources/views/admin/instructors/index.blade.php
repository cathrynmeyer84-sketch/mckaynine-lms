<x-app-layout :title="'Instructors'">
<div class="page-content space-y-6">

    <div class="page-header">
        <div>
            <h1 class="page-title">Instructors</h1>
            <p class="page-subtitle">Manage training instructors</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Invite form --}}
    <div class="card">
        <h2 class="text-sm font-semibold text-navy mb-1">Invite a New Instructor</h2>
        <p class="text-xs text-gray-500 mb-4">
            They'll receive an email with a link to set up their password, bio, and profile photo.
        </p>

        <form method="POST" action="{{ route('admin.instructors.invite') }}"
              class="flex flex-col sm:flex-row gap-3 items-start flex-wrap">
            @csrf

            <div class="w-36">
                <label class="block text-xs font-semibold text-gray-500 mb-1">First name <span class="text-red-400">*</span></label>
                <input type="text" name="first_name" required
                       value="{{ old('first_name') }}"
                       placeholder="Sarah"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand @error('first_name') border-red-300 @enderror">
                @error('first_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="w-36">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Last name <span class="text-red-400">*</span></label>
                <input type="text" name="last_name" required
                       value="{{ old('last_name') }}"
                       placeholder="Jones"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand @error('last_name') border-red-300 @enderror">
                @error('last_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex-1 min-w-48">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Email address <span class="text-red-400">*</span></label>
                <input type="email" name="email" required
                       value="{{ old('email') }}"
                       placeholder="instructor@example.com"
                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand @error('email') border-red-300 @enderror">
                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="sm:mt-5">
                <button type="submit" class="btn btn-primary whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Send Invite
                </button>
            </div>
        </form>
    </div>

    {{-- Pending invitations --}}
    @if($pendingInvites->isNotEmpty())
    <div class="card !p-0 overflow-hidden">
        <div class="px-4 py-3 bg-amber-50 border-b border-amber-100 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            <p class="text-sm font-semibold text-amber-800">{{ $pendingInvites->count() }} pending invitation{{ $pendingInvites->count() !== 1 ? 's' : '' }}</p>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
                @foreach($pendingInvites as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-navy">{{ $inv->name }}</p>
                        <p class="text-xs text-gray-400">{{ $inv->email }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400 hidden sm:table-cell whitespace-nowrap">
                        Expires {{ $inv->expires_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 justify-end">
                            <form method="POST" action="{{ route('admin.instructors.invitations.resend', $inv) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline">Resend</button>
                            </form>
                            <form method="POST" action="{{ route('admin.instructors.invitations.cancel', $inv) }}"
                                  onsubmit="return confirm('Cancel this invitation?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm text-red-400 border-red-100 hover:bg-red-50">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Active instructors grid --}}
    @if($instructors->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($instructors as $instructor)
        <div class="card flex flex-col">
            <div class="flex items-start gap-4 mb-4">
                @if($instructor->profile_photo_path)
                <img src="{{ Storage::url($instructor->profile_photo_path) }}"
                     alt="{{ $instructor->first_name }}"
                     class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                @else
                <div class="w-12 h-12 rounded-xl bg-brand flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-lg font-bold">{{ substr($instructor->first_name, 0, 1) }}</span>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900">{{ $instructor->first_name }} {{ $instructor->last_name }}</p>
                    @if($instructor->email)
                        <p class="text-xs text-gray-500 truncate">{{ $instructor->email }}</p>
                    @endif
                    @if($instructor->phone)
                        <p class="text-xs text-gray-500">{{ $instructor->phone }}</p>
                    @endif
                </div>
                @if($instructor->is_active)
                    <span class="badge badge-active text-xs flex-shrink-0">Active</span>
                @else
                    <span class="badge text-xs flex-shrink-0">Inactive</span>
                @endif
            </div>

            @if($instructor->bio)
            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $instructor->bio }}</p>
            @endif

            <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">{{ $instructor->classes->count() }} class(es)</span>
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="btn-outline btn-sm">View Profile</a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <p>No instructors yet — invite your first one above.</p>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
