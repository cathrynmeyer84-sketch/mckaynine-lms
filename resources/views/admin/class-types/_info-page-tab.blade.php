<form method="POST" action="{{ route('admin.class-types.info-page.update', $classType) }}" enctype="multipart/form-data">
    @csrf

    {{-- Visibility --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Visibility</h2>
        <div class="space-y-3 mb-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="info_page_enabled" value="1" class="rounded border-gray-300 text-brand"
                    {{ $classType->info_page_enabled ? 'checked' : '' }}>
                <span class="text-sm font-medium text-navy">Enable public info page</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_public" value="1" class="rounded border-gray-300 text-brand"
                    {{ $classType->is_public ? 'checked' : '' }}>
                <span class="text-sm font-medium text-navy">Visible without login</span>
                <span class="text-xs text-gray-400">(if unchecked, only logged-in users can view)</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="individual_class_pages" value="1" class="rounded border-gray-300 text-brand"
                    {{ $classType->individual_class_pages ? 'checked' : '' }}>
                <span class="text-sm font-medium text-navy">Individual class pages</span>
                <span class="text-xs text-gray-400">(each class gets its own sub-page; type page shows a "choose a class" grid)</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_entry_class" value="1" class="rounded border-gray-300 text-brand"
                    {{ $classType->is_entry_class ? 'checked' : '' }}>
                <span class="text-sm font-medium text-navy">Entry class</span>
                <span class="text-xs text-gray-400">(only one class type can be marked as entry — this drives the "Enrol Now" button and the puppy enrolment form for dogs under 4 months)</span>
            </label>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Page Template</label>
                <select name="page_template" class="input">
                    <option value="default" @selected(($classType->page_template ?? 'default') === 'default')>Default</option>
                    <option value="puppy" @selected($classType->page_template === 'puppy')>Term Classes</option>
                    <option value="eo_cgc" @selected($classType->page_template === 'eo_cgc')>Monthly Classes</option>
                </select>
            </div>
            <div>
                <label class="label">URL Slug</label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400 shrink-0">/classes/</span>
                    <input type="text" name="slug" class="input flex-1" placeholder="e.g. puppy-class"
                        value="{{ old('slug', $classType->slug) }}">
                </div>
                @error('slug')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                @if($classType->info_page_enabled && $classType->slug)
                <a href="{{ route('class-info.show', $classType->slug) }}" target="_blank"
                    class="text-xs text-brand hover:underline mt-1 inline-block">View public page →</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Colours --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Colours</h2>
        @php
        $colourThemes = [
            'forest' => ['name' => 'Forest', 'swatches' => ['#365236', '#C8DFD6', '#D6C2B5', '#4C7AC6']],
            'ocean'  => ['name' => 'Ocean',  'swatches' => ['#001d6d', '#3964b0', '#d8e3f5', '#eaecf0']],
            'slate'  => ['name' => 'Slate',  'swatches' => ['#3d3530', '#c4714a', '#f0ece8', '#6b6560']],
        ];
        $currentTheme    = old('color_theme', $classType->color_theme ?? 'forest');
        $overlayDefaults = ['forest' => '#365236', 'ocean' => '#001d6d', 'slate' => '#3d3530'];
        @endphp

        <div class="space-y-4">
            <div>
                <label class="label">Colour Theme</label>
                <div class="grid grid-cols-3 gap-3 mt-1">
                    @foreach($colourThemes as $key => $theme)
                    <label class="cursor-pointer">
                        <input type="radio" name="color_theme" value="{{ $key }}"
                            {{ $currentTheme === $key ? 'checked' : '' }} class="sr-only peer">
                        <div class="rounded-xl overflow-hidden border-2 border-transparent peer-checked:border-brand transition-all shadow-sm">
                            <div class="flex h-10">
                                @foreach($theme['swatches'] as $swatch)
                                <div style="background:{{ $swatch }};flex:1;"></div>
                                @endforeach
                            </div>
                            <div class="py-2 bg-gray-50 text-center text-xs font-semibold text-gray-700">{{ $theme['name'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="label">Hero Overlay Colour</label>
                <div class="flex items-center gap-3 mt-1">
                    <input type="color" name="hero_overlay_color"
                        value="{{ old('hero_overlay_color', $classType->hero_overlay_color ?: ($overlayDefaults[$currentTheme] ?? '#365236')) }}"
                        class="h-10 w-16 rounded-lg cursor-pointer border border-gray-300 p-0.5">
                    <span class="text-xs text-gray-400">Tint blended over the banner photo. Darker = more contrast for white text.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Hero Image --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Hero Image</h2>
        <p class="text-xs text-gray-400 mb-3">Recommended size: 1720 × 520px (desktop) · 800 × 500px (mobile).</p>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Desktop</label>
                @if($classType->image_path)
                <img src="{{ Storage::url($classType->image_path) }}" class="w-full h-28 object-cover rounded-xl mb-2">
                @endif
                <input type="file" name="image" accept="image/*" class="input">
            </div>
            <div>
                <label class="label">Mobile</label>
                @if($classType->image_mobile_path)
                <img src="{{ Storage::url($classType->image_mobile_path) }}" class="w-full h-28 object-cover rounded-xl mb-2">
                @endif
                <input type="file" name="image_mobile" accept="image/*" class="input">
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Call to Action</h2>
        <p class="text-xs text-gray-400 mb-3">What happens when someone clicks the main button on the public info page.</p>
        <div class="space-y-2">
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border border-gray-200 hover:bg-gray-50 has-[:checked]:border-brand has-[:checked]:bg-brand/5">
                <input type="radio" name="cta_type" value="enquire" class="mt-0.5 text-brand" {{ old('cta_type', $classType->cta_type ?? 'enquire') === 'enquire' ? 'checked' : '' }}>
                <div>
                    <p class="text-sm font-semibold text-navy">Enquire Now</p>
                    <p class="text-xs text-gray-400">Opens an enquiry form. Logged-in users get a pre-populated form; guests enter their contact details and we email the admin.</p>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border border-gray-200 hover:bg-gray-50 has-[:checked]:border-brand has-[:checked]:bg-brand/5">
                <input type="radio" name="cta_type" value="enrol" class="mt-0.5 text-brand" {{ old('cta_type', $classType->cta_type ?? 'enquire') === 'enrol' ? 'checked' : '' }}>
                <div>
                    <p class="text-sm font-semibold text-navy">Enrol Now</p>
                    <p class="text-xs text-gray-400">Takes the user directly to the enrolment form.</p>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border border-gray-200 hover:bg-gray-50 has-[:checked]:border-brand has-[:checked]:bg-brand/5">
                <input type="radio" name="cta_type" value="assessment" class="mt-0.5 text-brand" {{ old('cta_type', $classType->cta_type ?? 'enquire') === 'assessment' ? 'checked' : '' }}>
                <div>
                    <p class="text-sm font-semibold text-navy">Book an Assessment</p>
                    <p class="text-xs text-gray-400">Takes the user to the free assessment booking form.</p>
                </div>
            </label>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Page Content</h2>
        <div class="space-y-4">
            <div>
                <label class="label">Hero Heading</label>
                <input type="text" name="hero_heading" class="input" placeholder="e.g. Give your puppy the best start in life"
                    value="{{ old('hero_heading', $classType->hero_heading) }}">
                <p class="text-xs text-gray-400 mt-1">Large text displayed on the banner image. Leave blank to use the class type name.</p>
            </div>
            <div>
                <label class="label">Tagline</label>
                <input type="text" name="tagline" class="input" placeholder="Short strapline shown under the heading"
                    value="{{ old('tagline', $classType->tagline) }}">
            </div>
            <div>
                <label class="label">About this Class</label>
                <textarea name="about" rows="5" class="input" placeholder="What the class covers, who it's for, what handlers can expect…">{{ old('about', $classType->about) }}</textarea>
            </div>
            <div>
                <label class="label">Promo Video URL (YouTube)</label>
                <input type="url" name="promo_video_url" class="input" placeholder="https://www.youtube.com/watch?v=..."
                    value="{{ old('promo_video_url', $classType->promo_video_url) }}">
            </div>
        </div>
    </div>

    {{-- Page Sections --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Page Sections</h2>
        <p class="text-xs text-gray-400 mb-4">Leave any section blank to hide it on the public page.</p>
        <div class="space-y-4">
            <div>
                <label class="label">Trust Strip <span class="text-gray-400 font-normal">(shown under the banner)</span></label>
                <textarea name="trust_strap" rows="2" class="input" placeholder="Trusted by owners since 1999&#10;Recommended by vets &amp; professionals">{{ old('trust_strap', $classType->trust_strap) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">One line per row.</p>
            </div>
            <div>
                <label class="label">What We Help With</label>
                <textarea name="helps_with" rows="6" class="input font-mono text-sm" placeholder="Nipping&#10;Chewing&#10;Lead Walking&#10;Confidence">{{ old('helps_with', $classType->helps_with) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">One item per line — shown as a checklist.</p>
            </div>
            <div>
                <label class="label">Requirements / When Can I Start?</label>
                <textarea name="age_requirements" rows="4" class="input" placeholder="Puppies should start at 10 to 14 weeks old…&#10;&#10;Pups MUST have had two vaccinations before starting.">{{ old('age_requirements', $classType->age_requirements) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Blank line between paragraphs.</p>
            </div>
            <div>
                <label class="label">What To Bring</label>
                <textarea name="what_to_bring" rows="5" class="input font-mono text-sm" placeholder="Flat buckle collar&#10;Light webbing lead (no chain leads)&#10;LOTS of small soft treats">{{ old('what_to_bring', $classType->what_to_bring) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">One item per line — shown as a bullet list.</p>
            </div>
            <div>
                <label class="label">How To Join — Steps</label>
                <textarea name="how_to_join_steps" rows="4" class="input" placeholder="Click Enquire Now — we'll match you to the right class&#10;Send us a copy of the vaccination card&#10;We'll confirm payment and joining details once everything is set">{{ old('how_to_join_steps', $classType->how_to_join_steps) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">
                    One step per line — numbered automatically.
                    To add a link: <code class="bg-gray-100 rounded px-1 font-mono">[link text](https://...)</code>
                    — e.g. <code class="bg-gray-100 rounded px-1 font-mono">[download the form](https://...)</code>
                </p>
            </div>
            <div>
                <label class="label">How To Join — Small Print</label>
                <textarea name="joining_notes" rows="2" class="input font-mono text-sm" placeholder="Cut-off is one business day before your first lesson&#10;Confirmation sent once we receive your docs">{{ old('joining_notes', $classType->joining_notes) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">One note per line.</p>
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
                <p class="text-xs text-gray-400 mt-1">Shown on the info page. Specific upcoming dates with available spots are listed below this automatically.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Cost From (ZAR)</label>
                    <input type="number" name="cost_from" class="input" step="0.01" min="0"
                        placeholder="e.g. 195.00" value="{{ old('cost_from', $classType->cost_from) }}">
                </div>
                <div>
                    <label class="label">Cost Notes</label>
                    <input type="text" name="cost_notes" class="input"
                        placeholder="e.g. Includes puppy starter kit"
                        value="{{ old('cost_notes', $classType->cost_notes) }}">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="label">Fees Image</label>
                    @if($classType->fees_image_path)
                    <img src="{{ Storage::url($classType->fees_image_path) }}" class="w-full h-20 object-cover rounded-lg mb-2">
                    @endif
                    <input type="file" name="fees_image" accept="image/*" class="input">
                </div>
                <div>
                    <label class="label">Fees Image (Mobile)</label>
                    @if($classType->fees_image_mobile_path)
                    <img src="{{ Storage::url($classType->fees_image_mobile_path) }}" class="w-full h-20 object-cover rounded-lg mb-2">
                    @endif
                    <input type="file" name="fees_image_mobile" accept="image/*" class="input">
                </div>
            </div>
        </div>
    </div>

    {{-- Gallery --}}
    <div class="card mb-6">
        <h2 class="card-title mb-4">Image Gallery</h2>
        @if($classType->gallery_images && count($classType->gallery_images))
        <div class="grid grid-cols-3 gap-3 mb-3">
            @foreach($classType->gallery_images as $path)
            <div class="relative">
                <img src="{{ Storage::url($path) }}" class="w-full h-24 object-cover rounded-xl">
                <label class="absolute top-1 right-1 bg-white/80 rounded-full p-1 cursor-pointer" title="Uncheck to remove">
                    <input type="checkbox" name="keep_gallery[]" value="{{ $path }}" checked class="rounded border-gray-300 text-brand">
                </label>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400 mb-4">Uncheck an image to remove it on save.</p>
        @endif
        <div>
            <label class="label">Add Images</label>
            <input type="file" name="gallery_add[]" accept="image/*" multiple class="input">
            <p class="text-xs text-gray-400 mt-1">Select multiple files at once.</p>
        </div>
    </div>

    {{-- Documents --}}
    <div class="card mb-6 border-dashed border-gray-200 bg-gray-50/50">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium text-navy mb-0.5">Need to link to a document in the steps above?</p>
                <p class="text-xs text-gray-500 mb-3">
                    Upload your PDFs and Word docs in the Document Library, then copy the link and paste it here using
                    <code class="bg-white border border-gray-200 rounded px-1 font-mono">[link text](URL)</code>.
                </p>
                <a href="{{ route('admin.documents.index') }}" target="_blank"
                   class="btn btn-outline btn-sm inline-flex items-center gap-1.5">
                    Open Document Library
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
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
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Name & Dog</label>
                    <input type="text" name="testimonial_name" class="input" placeholder="Sarah & Max"
                        value="{{ old('testimonial_name', $classType->testimonial_name) }}">
                </div>
                <div>
                    <label class="label">Photo</label>
                    @if($classType->testimonial_photo_path)
                    <img src="{{ Storage::url($classType->testimonial_photo_path) }}" class="w-12 h-12 object-cover rounded-full mb-2">
                    @endif
                    <input type="file" name="testimonial_photo" accept="image/*" class="input">
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        @if($classType->info_page_enabled && $classType->slug)
        <a href="{{ route('class-info.show', $classType->slug) }}?preview=1" target="_blank" class="btn btn-outline">Preview ↗</a>
        @else
        <span class="text-xs text-gray-400">Save and enable the page to preview it.</span>
        @endif
        <button type="submit" class="btn btn-primary">Save Info Page</button>
    </div>

</form>
