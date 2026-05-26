<x-app-layout :title="'Add Resource'">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.resources.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Add Resource</h1>
                <p class="page-subtitle">Create a new training resource</p>
            </div>
        </div>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.resources.store') }}" class="space-y-6" enctype="multipart/form-data"
            x-data="{ hasLink: {{ old('external_url') ? 'true' : 'false' }}, hasContent: true }">
            @csrf

            <div class="card">
                <h2 class="form-section-title">Resource Details</h2>
                <div class="form-section space-y-4">

                    <div>
                        <label class="form-label" for="title">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}"
                            class="form-input w-full" required placeholder="e.g. Crate Training Guide">
                        @error('title')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label" for="category">Category</label>
                            <select id="category" name="category" class="form-select w-full">
                                <option value="">Select category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="sort_order">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}"
                                class="form-input w-full" min="0">
                            <p class="text-xs text-gray-400 mt-1">Lower = displayed first</p>
                        </div>
                    </div>

                    {{-- Class type restrictions --}}
                    <div>
                        <label class="form-label">Show for Class Types</label>
                        <p class="text-xs text-gray-400 mb-2">Leave blank to show for all handlers</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach($classTypes as $type)
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="checkbox" name="class_categories[]" value="{{ $type->id }}"
                                    class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                                    @checked(in_array($type->id, old('class_categories', [])))>
                                {{ $type->name }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <h2 class="form-section-title">Content</h2>
                <div class="form-section space-y-4">

                    {{-- Type toggle --}}
                    <div class="flex gap-3">
                        <button type="button" @click="hasLink = false; hasContent = true"
                            :class="!hasLink ? 'bg-brand text-white border-brand' : 'bg-white text-gray-600 border-gray-300'"
                            class="btn-sm border rounded-xl px-4">Article / Text</button>
                        <button type="button" @click="hasLink = true; hasContent = false"
                            :class="hasLink ? 'bg-brand text-white border-brand' : 'bg-white text-gray-600 border-gray-300'"
                            class="btn-sm border rounded-xl px-4">External Link</button>
                    </div>

                    <div x-show="!hasLink">
                        <label class="form-label">Article Content</label>
                        <textarea name="content" rows="8" class="form-textarea w-full"
                            placeholder="Write the resource content here. Markdown formatting supported.">{{ old('content') }}</textarea>
                    </div>

                    <div x-show="hasLink">
                        <label class="form-label">External URL</label>
                        <input type="url" name="external_url" value="{{ old('external_url') }}"
                            class="form-input w-full" placeholder="https://...">
                        @error('external_url')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                </div>
            </div>

            <div class="card">
                <h2 class="form-section-title">Cover Image</h2>
                <div class="form-section">
                    <label class="form-label">Image <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="file" name="image_file" accept="image/*" class="form-input w-full">
                    <p class="text-xs text-gray-400 mt-1">JPEG, PNG, WebP · max 5 MB · displayed at the top of the article</p>
                </div>
            </div>

            <div class="card">
                <h2 class="form-section-title">Visibility</h2>
                <div class="form-section">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="is_published" name="is_published" value="1"
                            class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand"
                            @checked(old('is_published'))>
                        <label for="is_published" class="text-sm font-medium text-gray-700">Publish now (visible to handlers)</label>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary btn-lg">Create Resource</button>
                <a href="{{ route('admin.resources.index') }}" class="btn-outline btn-lg">Cancel</a>
            </div>

        </form>
    </div>

</div>
</x-app-layout>
