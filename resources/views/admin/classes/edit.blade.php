<x-app-layout :title="'Edit: ' . $class->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Edit Class</h1>
                <p class="page-subtitle">{{ $class->name }}</p>
            </div>
        </div>
    </div>

    {{-- Update form --}}
    <form id="update-form" method="POST" action="{{ route('admin.classes.update', $class) }}">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="card">
                    <h2 class="form-section-title">Class Details</h2>
                    <div class="form-section space-y-4">

                        <div>
                            <label class="form-label">Class Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $class->name) }}"
                                class="form-input w-full" required>
                            @error('name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="form-label">Class Type <span class="text-red-500">*</span></label>
                            <select name="class_type_id" class="form-select w-full" required>
                                <option value="">— Select class type —</option>
                                @foreach($classTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('class_type_id', $class->class_type_id) == $type->id)>
                                    {{ $type->name }} ({{ $type->duration_label }}){{ $type->has_structured_content ? ' · structured content' : '' }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1"><a href="{{ route('admin.class-types.index') }}" class="text-brand underline">Manage class types</a></p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" value="{{ old('start_date', $class->start_date?->format('Y-m-d')) }}" class="form-input w-full">
                            </div>
                            <div>
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" value="{{ old('end_date', $class->end_date?->format('Y-m-d')) }}" class="form-input w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Class Start Time</label>
                                <input type="time" name="start_time" value="{{ old('start_time', $class->start_time) }}" class="form-input w-full">
                            </div>
                            <div>
                                <label class="form-label">Class End Time</label>
                                <input type="time" name="end_time" value="{{ old('end_time', $class->end_time) }}" class="form-input w-full">
                                <p class="text-xs text-gray-400 mt-1">Content emails send 1 hr after end time</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Max Capacity</label>
                                <input type="number" name="max_capacity" value="{{ old('max_capacity', $class->max_capacity) }}" class="form-input w-full" min="1">
                            </div>
                            <div>
                                <label class="form-label">Location</label>
                                <input type="text" name="location" value="{{ old('location', $class->location) }}" class="form-input w-full">
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-textarea w-full">{{ old('description', $class->description) }}</textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="has_final_exam" name="has_final_exam" value="1"
                                class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                @checked(old('has_final_exam', $class->has_final_exam))>
                            <label for="has_final_exam" class="text-sm font-medium text-gray-700">Has final exam / grading</label>
                        </div>
                    </div>
                </div>

            </div>

            <div class="space-y-6">

                {{-- Instructors --}}
                <div class="card">
                    <h2 class="form-section-title">Instructors</h2>
                    @php $assignedIds = $class->instructors->pluck('id')->toArray(); @endphp
                    <div class="space-y-2">
                        @forelse($instructors as $instructor)
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" name="instructor_ids[]" value="{{ $instructor->id }}"
                                class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                @checked(in_array($instructor->id, old('instructor_ids', $assignedIds)))>
                            <span class="text-sm font-medium text-gray-900">{{ $instructor->first_name }} {{ $instructor->last_name }}</span>
                        </label>
                        @empty
                        <p class="text-sm text-gray-500">No instructors available.</p>
                        @endforelse
                    </div>
                </div>

                @if($class->classType?->duration_type === 'term')
                <div class="card">
                    <h2 class="form-section-title">Next Class Options</h2>
                    <p class="text-xs text-gray-400 mb-3">Shown in the completion message sent to handlers. You can link to class types (shows all available times) and/or specific upcoming classes.</p>

                    @php
                        $nextClassIds     = old('next_class_ids', $class->next_class_ids ?? []);
                        $nextClassTypeIds = old('next_class_type_ids', $class->next_class_type_ids ?? []);
                        $allClassTypes    = \App\Models\ClassType::where('info_page_enabled', true)->whereNotNull('slug')->orderBy('name')->get();
                    @endphp

                    @if($allClassTypes->isNotEmpty())
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Class Types <span class="font-normal normal-case text-gray-400">(links to type overview page with all available times)</span></p>
                    <div class="space-y-2 mb-4">
                        @foreach($allClassTypes as $ct)
                        <label class="flex items-start gap-3 p-2.5 bg-brand/5 rounded-xl cursor-pointer hover:bg-brand/10">
                            <input type="checkbox" name="next_class_type_ids[]" value="{{ $ct->id }}"
                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                @checked(in_array($ct->id, $nextClassTypeIds))>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $ct->name }}</p>
                                @if($ct->general_schedule)<p class="text-xs text-gray-400">{{ $ct->general_schedule }}</p>@endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif

                    @if($otherClasses->isNotEmpty())
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Specific Classes <span class="font-normal normal-case text-gray-400">(links directly to a class instance)</span></p>
                    <div class="space-y-2 max-h-56 overflow-y-auto">
                        @foreach($otherClasses as $other)
                        <label class="flex items-start gap-3 p-2.5 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100">
                            <input type="checkbox" name="next_class_ids[]" value="{{ $other->id }}"
                                class="mt-0.5 w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                @checked(in_array($other->id, $nextClassIds))>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $other->name }}</p>
                                <p class="text-xs text-gray-400">{{ $other->classType?->name }}
                                    @if($other->start_date) · {{ $other->start_date->format('d M Y') }}@endif
                                </p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif

                </div>
                @endif

                <div class="card">
                    {{-- button references the update form by ID, works outside nested forms --}}
                    <button type="submit" form="update-form" class="btn-primary w-full btn-lg">Save Changes</button>
                    <a href="{{ route('admin.classes.show', $class) }}" class="btn-outline w-full mt-3 block text-center">Cancel</a>
                </div>

            </div>
        </div>
    </form>

    {{-- Delete form is completely separate from the update form --}}
    <div class="mt-6 lg:flex lg:justify-end">
        <div class="lg:w-1/3 card border-red-100">
            <p class="text-xs text-gray-500 mb-3">Deleting this class will permanently remove all class dates, enrolments, registers, and grades associated with it.</p>
            <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                onsubmit="return confirm('Delete {{ addslashes($class->name) }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-2 px-4 rounded-xl border-2 border-red-200 text-red-600 text-sm font-semibold hover:bg-red-50 transition-colors">
                    Delete Class
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>
