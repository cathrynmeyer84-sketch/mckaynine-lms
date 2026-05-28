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

{{-- Structured content --}}
@if($grouped['structured']->isNotEmpty())
<div class="mb-8">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Structured Content</h2>
    <div class="grid gap-4">
        @foreach($grouped['structured'] as $type)
        @include('admin.class-types._index-row', ['type' => $type])
        @endforeach
    </div>
</div>
@endif

{{-- Monthly / ongoing --}}
@if($grouped['monthly']->isNotEmpty())
<div class="mb-8">
    <h2 class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Monthly</h2>
    <div class="grid gap-4">
        @foreach($grouped['monthly'] as $type)
        @include('admin.class-types._index-row', ['type' => $type])
        @endforeach
    </div>
</div>
@endif

@endif

</div>
@endsection
