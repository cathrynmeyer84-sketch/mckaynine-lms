@extends('layouts.app')

@section('title', 'Invitations')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Invitations</h1>
        <p class="page-subtitle">Invite existing students to create their account</p>
    </div>
</div>

<div class="page-content space-y-6">

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- CSV results --}}
@if(session('csv_sent') && count(session('csv_sent')))
<div class="card !p-0 overflow-hidden">
    <div class="px-4 py-3 bg-green-50 border-b border-green-100 flex items-center gap-2">
        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-semibold text-green-800">{{ count(session('csv_sent')) }} invitation{{ count(session('csv_sent')) !== 1 ? 's' : '' }} sent</p>
    </div>
    <ul class="divide-y divide-gray-100 max-h-48 overflow-y-auto">
        @foreach(session('csv_sent') as $email)
        <li class="px-4 py-2 text-sm text-gray-700">{{ $email }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('csv_skipped') && count(session('csv_skipped')))
<div class="card !p-0 overflow-hidden">
    <div class="px-4 py-3 bg-amber-50 border-b border-amber-100 flex items-center gap-2">
        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-semibold text-amber-800">{{ count(session('csv_skipped')) }} row{{ count(session('csv_skipped')) !== 1 ? 's' : '' }} skipped</p>
    </div>
    <table class="w-full text-sm">
        <tbody class="divide-y divide-gray-100">
            @foreach(session('csv_skipped') as $item)
            <tr>
                <td class="px-4 py-2 text-gray-700">{{ $item['value'] }}</td>
                <td class="px-4 py-2 text-amber-600">{{ $item['reason'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Send invitation --}}
<div class="card">
    <h2 class="text-sm font-semibold text-navy mb-1">Send an Invitation</h2>
    <p class="text-xs text-gray-500 mb-4">
        The student will receive an email with a link to create their account and add their dog(s).
        The link is valid for 14 days.
    </p>

    <form method="POST" action="{{ route('admin.invitations.store') }}"
          class="flex flex-col sm:flex-row gap-3 items-start">
        @csrf

        <div class="flex-1 min-w-0">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Email address <span class="text-red-400">*</span></label>
            <input type="email" name="email" required
                   value="{{ old('email') }}"
                   placeholder="student@example.com"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/10 @error('email') border-red-300 @enderror">
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="w-48">
            <label class="block text-xs font-semibold text-gray-500 mb-1">First name <span class="text-gray-300">(optional)</span></label>
            <input type="text" name="name"
                   value="{{ old('name') }}"
                   placeholder="e.g. Sarah"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand focus:ring-2 focus:ring-brand/10">
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

{{-- CSV batch upload --}}
<div class="card"
     x-data="{ fileName: '' }">
    <div class="flex items-start justify-between gap-4 mb-1">
        <div>
            <h2 class="text-sm font-semibold text-navy">Batch Upload via CSV</h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Upload a CSV file with one student per row. Two columns: <code class="bg-gray-100 rounded px-1 font-mono">email</code> and <code class="bg-gray-100 rounded px-1 font-mono">name</code> (name is optional).
                A header row is detected automatically.
            </p>
        </div>
        <a href="{{ route('admin.invitations.sample-csv') }}"
           class="btn btn-sm btn-outline whitespace-nowrap flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Sample CSV
        </a>
    </div>

    <form method="POST" action="{{ route('admin.invitations.csv') }}"
          enctype="multipart/form-data"
          class="mt-4 flex flex-col sm:flex-row gap-3 items-start">
        @csrf

        <div class="flex-1 min-w-0">
            <label
                class="flex items-center gap-3 border border-dashed border-gray-300 rounded-lg px-4 py-3 cursor-pointer hover:border-brand hover:bg-brand/5 transition-colors"
                @click="$refs.csvInput.click()">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="text-sm text-gray-500" x-text="fileName || 'Choose CSV file…'"></span>
            </label>
            <input type="file" name="csv_file" accept=".csv,text/csv"
                   x-ref="csvInput" class="hidden"
                   @change="fileName = $event.target.files[0]?.name || ''">
            @error('csv_file')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="sm:mt-0">
            <button type="submit" class="btn btn-primary whitespace-nowrap"
                    :disabled="!fileName">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Send All
            </button>
        </div>
    </form>
</div>

{{-- Invitations list --}}
@if($invitations->isEmpty())
<div class="card text-center py-12">
    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
    <p class="text-sm text-gray-400">No invitations sent yet.</p>
</div>
@else
<div class="card !p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">Recipient</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Sent</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Expires</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($invitations as $inv)
            @php $status = $inv->status; @endphp
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <p class="font-medium text-navy">{{ $inv->email }}</p>
                    @if($inv->name)<p class="text-xs text-gray-400">{{ $inv->name }}</p>@endif
                </td>
                <td class="px-4 py-3 text-gray-500 hidden sm:table-cell whitespace-nowrap">
                    {{ $inv->created_at->format('d M Y') }}
                </td>
                <td class="px-4 py-3 text-gray-400 hidden md:table-cell whitespace-nowrap">
                    {{ $inv->expires_at->format('d M Y') }}
                </td>
                <td class="px-4 py-3">
                    @if($status === 'used')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2.5 py-0.5">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Signed up
                        </span>
                    @elseif($status === 'expired')
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-2.5 py-0.5">
                            Expired
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            Pending
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        @if($status === 'pending')
                        <form method="POST" action="{{ route('admin.invitations.resend', $inv) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline whitespace-nowrap"
                                    title="Resend invitation email">
                                Resend
                            </button>
                        </form>
                        @endif

                        <form method="POST" action="{{ route('admin.invitations.destroy', $inv) }}"
                              onsubmit="return confirm('Delete this invitation?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm text-red-400 border-red-100 hover:bg-red-50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($invitations->hasPages())
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $invitations->links() }}
    </div>
    @endif
</div>
@endif

</div>
@endsection
