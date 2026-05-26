<x-app-layout :title="'Create Class'">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Create Class</h1>
                <p class="page-subtitle">Set up a new training class</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.classes.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Main form --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="card">
                    <h2 class="form-section-title">Class Details</h2>
                    <div class="form-section space-y-4">

                        <div>
                            <label class="form-label" for="name">Class Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-input w-full" required placeholder="e.g. Puppy Class – Term 1 2026">
                            @error('name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label" for="class_type_id">Class Type <span class="text-red-500">*</span></label>
                            <select id="class_type_id" name="class_type_id" class="form-select w-full" required>
                                <option value="">— Select class type —</option>
                                @foreach($classTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('class_type_id') == $type->id)>
                                    {{ $type->name }} ({{ $type->duration_label }}){{ $type->has_structured_content ? ' · structured content' : '' }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1"><a href="{{ route('admin.class-types.index') }}" class="text-brand underline">Manage class types</a></p>
                        </div>

                        {{-- Date range --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label" for="start_date">Start Date <span class="text-red-500">*</span></label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                    class="form-input w-full" required>
                                @error('start_date')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="end_date">End Date <span class="text-red-500">*</span></label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                                    class="form-input w-full" required>
                                @error('end_date')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Class time --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label" for="start_time">Class Start Time</label>
                                <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}"
                                    class="form-input w-full">
                            </div>
                            <div>
                                <label class="form-label" for="end_time">Class End Time</label>
                                <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}"
                                    class="form-input w-full">
                                <p class="text-xs text-gray-400 mt-1">Weekly content emails at 1 hr after end time</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label" for="max_capacity">Max Capacity</label>
                                <input type="number" id="max_capacity" name="max_capacity" value="{{ old('max_capacity') }}"
                                    class="form-input w-full" min="1" placeholder="e.g. 8">
                            </div>
                            <div>
                                <label class="form-label" for="location">Location</label>
                                <input type="text" id="location" name="location" value="{{ old('location') }}"
                                    class="form-input w-full" placeholder="e.g. Main Training Ground">
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="form-textarea w-full" placeholder="Optional class description...">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="has_final_exam" name="has_final_exam" value="1"
                                class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                @checked(old('has_final_exam'))>
                            <label for="has_final_exam" class="text-sm font-medium text-gray-700">This class has a final exam / grading</label>
                        </div>

                    </div>
                </div>

                {{-- Calendar note --}}
                <div class="card bg-brand/5 border border-brand/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-brand shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="text-sm font-medium text-navy">Dates auto-generated from calendar</p>
                            <p class="text-xs text-gray-500 mt-0.5">Weekly sessions will be created for every matching day between start and end date, skipping any days marked as off in the <a href="{{ route('admin.calendar.index') }}" class="text-brand underline">year calendar</a>.</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">

                {{-- Instructors --}}
                <div class="card">
                    <h2 class="form-section-title">Instructors</h2>
                    <div class="space-y-2">
                        @forelse($instructors as $instructor)
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                            <input type="checkbox" name="instructor_ids[]" value="{{ $instructor->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                @checked(in_array($instructor->id, old('instructor_ids', [])))>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $instructor->first_name }} {{ $instructor->last_name }}</p>
                                @if($instructor->phone)
                                <p class="text-xs text-gray-500">{{ $instructor->phone }}</p>
                                @endif
                            </div>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500">No instructors available.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card">
                    <button type="submit" class="btn-primary w-full btn-lg">Create Class</button>
                    <a href="{{ route('admin.classes.index') }}" class="btn-outline w-full mt-3 block text-center">Cancel</a>
                </div>

            </div>
        </div>
    </form>

</div>
</x-app-layout>
