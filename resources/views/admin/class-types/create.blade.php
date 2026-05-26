@extends('layouts.app')

@section('title', 'New Class Type')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">New Class Type</h1>
        <p class="page-subtitle">Define the course structure and whether it includes weekly content</p>
    </div>
    <a href="{{ route('admin.class-types.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content max-w-2xl">

<div class="card">
    <form action="{{ route('admin.class-types.store') }}" method="POST"
        x-data="{
            durationType: 'term',
            hasContent: false,
        }">
        @csrf

        <div class="space-y-5">
            <div>
                <label class="label">Class type name <span class="text-red-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g. Puppy Class, CGC Bronze, Elementary Obedience"
                    class="input w-full" required>
            </div>

            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="2" class="input w-full"
                    placeholder="Brief overview of what this course covers">{{ old('description') }}</textarea>
            </div>

            {{-- Duration type --}}
            <div>
                <label class="label">Course duration <span class="text-red-400">*</span></label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="duration_type" value="term"
                            x-model="durationType" class="text-navy" checked>
                        <span class="text-sm font-medium text-gray-700">Term (fixed number of weeks)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="duration_type" value="ongoing"
                            x-model="durationType" class="text-navy">
                        <span class="text-sm font-medium text-gray-700">Ongoing (monthly / yearly)</span>
                    </label>
                </div>
            </div>

            <div x-show="durationType === 'term'" class="space-y-4">
                <div>
                    <label class="label">Number of weeks</label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="term_weeks" value="{{ old('term_weeks') }}"
                            min="1" max="52" placeholder="e.g. 6"
                            class="input w-28">
                        <span class="text-sm text-gray-500">weeks per term</span>
                    </div>
                </div>
                <div>
                    <label class="label">Course price per term</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">R</span>
                        <input type="number" name="course_price" step="0.01" min="0"
                            value="{{ old('course_price') }}"
                            class="input w-36" placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Standard price charged per dog for a full term. Used in instructor fee calculations.</p>
                </div>
            </div>

            <div x-show="durationType === 'ongoing'" class="space-y-4">
                <div>
                    <label class="label">Billing period</label>
                    <select name="billing_period" class="input w-48">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <div>
                    <label class="label">Monthly fee per dog</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">R</span>
                        <input type="number" name="monthly_fee_per_dog" step="0.01" min="0"
                            value="{{ old('monthly_fee_per_dog') }}"
                            class="input w-36" placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Standard fee charged per dog per month. Used in instructor fee calculations.</p>
                </div>
            </div>

            {{-- Structured content + grading --}}
            <div class="border-t border-stone/30 pt-5 space-y-3">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="has_structured_content" value="1"
                        x-model="hasContent" class="mt-0.5 text-navy">
                    <div>
                        <p class="text-sm font-medium text-gray-700">This course has structured weekly content</p>
                        <p class="text-xs text-gray-400 mt-0.5">Videos, notes, and practice tasks that go out to handlers each week</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="has_grading" value="1" class="mt-0.5 text-navy">
                    <div>
                        <p class="text-sm font-medium text-gray-700">This course has a grading / final exam</p>
                        <p class="text-xs text-gray-400 mt-0.5">Enables the Grading tab to configure exercises and scoring</p>
                    </div>
                </label>
            </div>

            <div x-show="hasContent" x-cloak>
                <p class="text-sm text-gray-500 bg-brand/5 border border-brand/20 rounded-lg px-4 py-3">
                    After saving, you'll be taken to the content editor where you can add per-week videos, descriptions, and practice tasks.
                </p>
            </div>
        </div>

        <div class="flex gap-3 mt-8">
            <button type="submit" class="btn btn-primary">Create Class Type</button>
            <a href="{{ route('admin.class-types.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

</div>
@endsection
