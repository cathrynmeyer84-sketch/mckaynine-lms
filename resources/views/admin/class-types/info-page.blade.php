<x-app-layout :title="$classType->name . ' — Info Page'">
<div class="page-content max-w-3xl">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.class-types.show', $classType) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">{{ $classType->name }}</h1>
                <p class="page-subtitle">Public Info Page</p>
            </div>
        </div>
        @if($classType->info_page_enabled && $classType->slug)
        <a href="{{ route('class-info.show', $classType->slug) }}" target="_blank" class="btn-outline btn-sm">
            View Page
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert-success mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.class-types.info-page.update', $classType) }}" enctype="multipart/form-data" id="info-page-form">
        @csrf

        {{-- Visibility --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Visibility</h2>
            <div class="space-y-3">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="info_page_enabled" value="1" class="rounded border-gray-300 text-brand"
                        {{ $classType->info_page_enabled ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-navy">Enable public info page</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_public" value="1" class="rounded border-gray-300 text-brand"
                        {{ $classType->is_public ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-navy">Visible without login</span>
                    <span class="text-xs text-gray-400">(if unchecked, only logged-in users can view)</span>
                </label>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Page Template</label>
                    <select name="page_template" class="input">
                        <option value="default" @selected(($classType->page_template ?? 'default') === 'default')>Default</option>
                        <option value="puppy" @selected($classType->page_template === 'puppy')>Puppy Class</option>
                        <option value="eo_cgc" @selected($classType->page_template === 'eo_cgc')>EO / CGC</option>
                    </select>
                </div>
                <div>
                    <label class="label">URL Slug</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-400">/classes/</span>
                        <input type="text" name="slug" class="input flex-1" placeholder="e.g. puppy-class"
                            value="{{ old('slug', $classType->slug) }}">
                    </div>
                    @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Hero --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Hero Image</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Desktop Hero Image</label>
                    @if($classType->image_path)
                    <img src="{{ Storage::url($classType->image_path) }}" class="w-full h-28 object-cover rounded-xl mb-2">
                    @endif
                    <input type="file" name="image" accept="image/*" class="input">
                </div>
                <div>
                    <label class="label">Mobile Hero Image</label>
                    @if($classType->image_mobile_path)
                    <img src="{{ Storage::url($classType->image_mobile_path) }}" class="w-full h-28 object-cover rounded-xl mb-2">
                    @endif
                    <input type="file" name="image_mobile" accept="image/*" class="input">
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Page Content</h2>
            <div class="space-y-4">
                <div>
                    <label class="label">Tagline</label>
                    <input type="text" name="tagline" class="input" placeholder="Short strapline shown under the class name"
                        value="{{ old('tagline', $classType->tagline) }}">
                </div>
                <div>
                    <label class="label">About this Class</label>
                    <textarea name="about" rows="6" class="input" placeholder="What the class covers, who it's for, what handlers can expect…">{{ old('about', $classType->about) }}</textarea>
                </div>
                <div>
                    <label class="label">Promo Video URL (YouTube)</label>
                    <input type="url" name="promo_video_url" class="input" placeholder="https://www.youtube.com/watch?v=..."
                        value="{{ old('promo_video_url', $classType->promo_video_url) }}">
                </div>
            </div>
        </div>

        {{-- Schedule & Pricing --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Schedule & Pricing</h2>
            <div class="space-y-4">
                <div>
                    <label class="label">General Schedule</label>
                    <input type="text" name="general_schedule" class="input"
                        placeholder="e.g. Monday evenings, 6:00pm – 7:00pm. New terms most school terms."
                        value="{{ old('general_schedule', $classType->general_schedule) }}">
                    <p class="text-xs text-gray-400 mt-1">Shown on the info page. Specific upcoming dates are listed below this.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Cost From (NZD)</label>
                        <input type="number" name="cost_from" class="input" step="0.01" min="0"
                            placeholder="e.g. 195.00"
                            value="{{ old('cost_from', $classType->cost_from) }}">
                    </div>
                    <div>
                        <label class="label">Cost Notes</label>
                        <input type="text" name="cost_notes" class="input"
                            placeholder="e.g. Includes puppy starter kit"
                            value="{{ old('cost_notes', $classType->cost_notes) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Gallery --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Image Gallery</h2>

            @if($classType->gallery_images && count($classType->gallery_images))
            <div class="grid grid-cols-3 gap-3 mb-4">
                @foreach($classType->gallery_images as $path)
                <div class="relative group">
                    <img src="{{ Storage::url($path) }}" class="w-full h-24 object-cover rounded-xl">
                    <label class="absolute top-1 right-1 bg-white/80 rounded-full p-1 cursor-pointer" title="Keep this image">
                        <input type="checkbox" name="keep_gallery[]" value="{{ $path }}" checked class="rounded border-gray-300 text-brand">
                    </label>
                    <span class="absolute bottom-1 left-1 text-xs text-white/80 bg-black/30 rounded px-1">uncheck to remove</span>
                </div>
                @endforeach
            </div>
            @endif

            <div>
                <label class="label">Add Images</label>
                <input type="file" name="gallery_add[]" accept="image/*" multiple class="input">
                <p class="text-xs text-gray-400 mt-1">You can select multiple files at once.</p>
            </div>
        </div>

        {{-- Testimonial --}}
        <div class="card mb-6">
            <h2 class="card-title mb-4">Testimonial</h2>
            <div class="space-y-4">
                <div>
                    <label class="label">Quote</label>
                    <textarea name="testimonial_text" rows="3" class="input" placeholder="What a handler said…">{{ old('testimonial_text', $classType->testimonial_text) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Name & Dog</label>
                        <input type="text" name="testimonial_name" class="input" placeholder="Sarah & Max"
                            value="{{ old('testimonial_name', $classType->testimonial_name) }}">
                    </div>
                    <div>
                        <label class="label">Photo</label>
                        @if($classType->testimonial_photo_path)
                        <img src="{{ Storage::url($classType->testimonial_photo_path) }}" class="w-16 h-16 object-cover rounded-full mb-2">
                        @endif
                        <input type="file" name="testimonial_photo" accept="image/*" class="input">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Save Info Page</button>
        </div>

    </form>

</div>
</x-app-layout>
