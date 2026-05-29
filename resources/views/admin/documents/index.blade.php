@extends('layouts.app')

@section('title', 'Document Library')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Document Library</h1>
        <p class="page-subtitle">Upload documents and copy their links for use on info pages</p>
    </div>
</div>

<div class="page-content space-y-6">

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Upload card --}}
<div class="card">
    <h2 class="text-sm font-semibold text-navy mb-1">Upload Documents</h2>
    <p class="text-xs text-gray-500 mb-4">
        PDF, Word, Excel, PowerPoint, CSV or plain text. Max 20 MB each.
        Once uploaded, copy the link and paste it into any info page step using
        <code class="bg-gray-100 rounded px-1 font-mono">[link text](URL)</code>.
    </p>
    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data"
          x-data="{ files: [], dragging: false }"
          @dragover.prevent="dragging = true"
          @dragleave.prevent="dragging = false"
          @drop.prevent="dragging = false; files = Array.from($event.dataTransfer.files); $refs.fileInput.files = $event.dataTransfer.files">
        @csrf

        <label
            :class="dragging ? 'border-brand bg-brand/5' : 'border-gray-300 bg-gray-50 hover:bg-gray-100'"
            class="flex flex-col items-center justify-center gap-2 border-2 border-dashed rounded-xl p-8 cursor-pointer transition-colors"
            @click="$refs.fileInput.click()">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="text-sm font-medium text-gray-600">
                <span x-show="files.length === 0">Drop files here, or <span class="text-brand underline">browse</span></span>
                <span x-show="files.length > 0" x-cloak x-text="files.length + ' file(s) selected'"></span>
            </span>
            <span class="text-xs text-gray-400">PDF · Word · Excel · PowerPoint · CSV · TXT</span>
        </label>

        <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.ppt,.pptx"
               x-ref="fileInput" class="hidden"
               @change="files = Array.from($event.target.files)">

        @error('files.*')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror

        <div x-show="files.length > 0" x-cloak class="mt-3 flex justify-end">
            <button type="submit" class="btn btn-primary">Upload @{{ files.length }} file(s)</button>
        </div>
    </form>
</div>

{{-- Documents list --}}
@if($documents->isEmpty())
<div class="card text-center py-12">
    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <p class="text-sm text-gray-400">No documents uploaded yet.</p>
</div>
@else
<div class="card !p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide">File</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden sm:table-cell">Size</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Uploaded</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($documents as $doc)
            <tr class="hover:bg-gray-50 transition-colors" x-data="{
                copied: false,
                docUrl: '{{ addslashes($doc->url) }}',
                copyUrl() {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(this.docUrl).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); });
                    } else {
                        const el = document.createElement('textarea');
                        el.value = this.docUrl;
                        el.style.position = 'fixed'; el.style.opacity = '0';
                        document.body.appendChild(el); el.select();
                        document.execCommand('copy');
                        document.body.removeChild(el);
                        this.copied = true; setTimeout(() => this.copied = false, 2000);
                    }
                }
            }">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @php
                            $ext = $doc->extension;
                            $iconColor = match(true) {
                                in_array($ext, ['pdf'])              => 'text-red-500',
                                in_array($ext, ['doc','docx'])       => 'text-blue-600',
                                in_array($ext, ['xls','xlsx','csv']) => 'text-green-600',
                                in_array($ext, ['ppt','pptx'])       => 'text-orange-500',
                                default                              => 'text-gray-400',
                            };
                        @endphp
                        <svg class="w-5 h-5 flex-shrink-0 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div class="min-w-0">
                            <p class="font-medium text-navy truncate max-w-xs">{{ $doc->name }}</p>
                            <p class="text-xs text-gray-400 font-mono truncate max-w-xs">{{ $doc->url }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-500 hidden sm:table-cell whitespace-nowrap">{{ $doc->formatted_size }}</td>
                <td class="px-4 py-3 text-gray-400 hidden md:table-cell whitespace-nowrap">{{ $doc->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        <button type="button"
                            @click="copyUrl()"
                            :class="copied ? 'border-green-300 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50'"
                            class="btn btn-sm border transition-colors whitespace-nowrap">
                            <span x-show="!copied">Copy link</span>
                            <span x-show="copied" x-cloak>✓ Copied!</span>
                        </button>
                        <a href="{{ $doc->url }}" target="_blank"
                           class="btn btn-sm btn-outline" title="Open file">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}"
                              onsubmit="return confirm('Delete {{ addslashes($doc->name) }}? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm text-red-400 border-red-100 hover:bg-red-50">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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

</div>
@endsection
