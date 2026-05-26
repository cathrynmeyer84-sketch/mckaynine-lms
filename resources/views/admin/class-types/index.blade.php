@extends('layouts.app')

@section('title', 'Class Types')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Class Types</h1>
        <p class="page-subtitle">Define course templates and structured weekly content</p>
    </div>
    <a href="{{ route('admin.class-types.create') }}" class="btn btn-primary">+ Add Class Type</a>
</div>

<div class="page-content">

@if(session('success'))
<div class="alert alert-success mb-6">{{ session('success') }}</div>
@endif

@if($classTypes->isEmpty())
<div class="card text-center py-12">
    <div class="empty-state-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
    </div>
    <h3 class="text-lg font-semibold text-navy mb-2">No class types yet</h3>
    <p class="text-gray-500 mb-6">Create your first class type to define course structure and weekly content templates.</p>
    <a href="{{ route('admin.class-types.create') }}" class="btn btn-primary">Get Started</a>
</div>
@else
<div class="grid gap-4">
    @foreach($classTypes as $type)
    <div class="card flex items-center gap-4">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.class-types.show', $type) }}" class="font-semibold text-navy hover:text-brand">{{ $type->name }}</a>
                <span class="badge {{ $type->duration_type === 'term' ? 'badge-upcoming' : 'badge-active' }}">
                    {{ $type->duration_label }}
                </span>
                @if($type->has_structured_content)
                <span class="badge badge-confirmed text-xs">Structured content</span>
                @endif
            </div>
            @if($type->description)
            <p class="text-sm text-gray-500 truncate">{{ $type->description }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-1">
                {{ $type->classes_count }} {{ Str::plural('class', $type->classes_count) }} using this type
                @if($type->has_structured_content && $type->weeks->count())
                · {{ $type->weeks->count() }} weeks of content
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.class-types.edit', $type) }}" class="btn btn-outline btn-sm">Edit</a>
            <form action="{{ route('admin.class-types.destroy', $type) }}" method="POST"
                onsubmit="return confirm('Delete {{ $type->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm text-red-500 border-red-200 hover:bg-red-50">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>
@endsection
