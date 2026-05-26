@extends('layouts.app')

@section('title', 'Assign to Class — ' . $handler->full_name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Assign to Class</h1>
        <p class="page-subtitle">{{ $handler->full_name }}</p>
    </div>
    <a href="{{ route('admin.handlers.show', $handler) }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content max-w-xl">
    <form action="{{ route('admin.handlers.assign-class.store', $handler) }}" method="POST" class="space-y-6">
        @csrf

        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Select Dog &amp; Class</h2>

            <div class="mb-4">
                <label class="form-label">Dog <span class="text-red-500">*</span></label>
                <select name="dog_id" class="form-select" required>
                    <option value="">Select a dog...</option>
                    @foreach($handler->dogs as $dog)
                    <option value="{{ $dog->id }}" {{ old('dog_id') == $dog->id ? 'selected' : '' }}>
                        {{ $dog->name }} ({{ $dog->breed ?? 'Unknown breed' }})
                    </option>
                    @endforeach
                </select>
                @error('dog_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Class <span class="text-red-500">*</span></label>
                <select name="class_id" class="form-select" required>
                    <option value="">Select a class...</option>
                    @foreach($availableClasses as $class)
                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                        @if($class->start_date) — starts {{ $class->start_date->format('d M Y') }} @endif
                        ({{ $class->enrolled_count }}/{{ $class->max_capacity ?? '∞' }})
                    </option>
                    @endforeach
                </select>
                @error('class_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">Confirm Enrolment</button>
            <a href="{{ route('admin.handlers.show', $handler) }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
