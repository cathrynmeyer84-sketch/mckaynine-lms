<x-app-layout :title="'Info Page — ' . $class->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.show', $class) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Info Page — {{ $class->name }}</h1>
                <p class="page-subtitle">Public-facing class info and enrolment page</p>
            </div>
        </div>
        @if($class->info_slug)
        <a href="{{ route('class-info.show', $class->info_slug) }}?preview=1" target="_blank" class="btn btn-outline btn-sm">Preview ↗</a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-6">
        <p class="text-sm font-semibold text-red-700 mb-1">Please fix the following errors:</p>
        <ul class="text-sm text-red-600 list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.classes.info-page.update', $class) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left column --}}
            <div class="space-y-4">

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Page Settings</h2>
                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="info_page_enabled" value="1" class="w-4 h-4 rounded" @checked($class->info_page_enabled)>
                            <span class="text-sm font-medium text-gray-700">Page enabled (publicly visible)</span>
                        </label>
                        <div>
                            <label class="form-label">URL Slug</label>
                            <div class="flex items-center gap-1 mt-1">
                                <span class="text-xs text-gray-400">/classes/</span>
                                <input type="text" name="info_slug" id="info_slug" value="{{ old('info_slug', $class->info_slug) }}"
                                    placeholder="puppy-honeydew-may-2026" class="form-input flex-1 text-sm">
                                <button type="button" title="Copy full URL"
                                    x-data="{ copied: false }"
                                    @click="navigator.clipboard.writeText('{{ url('/classes/') }}/' + document.getElementById('info_slug').value); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="text-gray-400 hover:text-navy transition-colors flex-shrink-0 ml-1">
                                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Auto-generated from class name if left blank</p>
                        </div>
                        <div>
                            <label class="form-label">Enrolment Form</label>
                            <select name="enrolment_form_type" class="form-select mt-1">
                                <option value="auto" @selected($class->enrolment_form_type === 'auto')>Auto-detect from DOB (recommended)</option>
                                <option value="puppy" @selected($class->enrolment_form_type === 'puppy')>Always Puppy form</option>
                                <option value="assessment" @selected($class->enrolment_form_type === 'assessment')>Always Assessment form</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="show_enrol_button" value="1" class="w-4 h-4 rounded" @checked($class->show_enrol_button ?? true)>
                            <span class="text-sm font-medium text-gray-700">Show "Enrol Now" button</span>
                        </label>
                    </div>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Course Fees</h2>
                    @if($class->classType?->course_price)
                    <p class="text-sm text-gray-500 bg-brand/5 border border-brand/20 rounded-lg px-3 py-2 mb-3">
                        Course price set on class type: <strong>R{{ number_format($class->classType->course_price, 2) }}</strong> per term.
                        To change it, update the <a href="{{ route('admin.class-types.edit', $class->classType) }}" class="text-brand underline">{{ $class->classType->name }} class type</a>.
                    </p>
                    @endif
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Enrolment Fee (R)</label>
                            <input type="number" name="enrolment_fee" step="0.01" min="0"
                                value="{{ old('enrolment_fee', $class->enrolment_fee) }}"
                                class="form-input mt-1" placeholder="265.00">
                        </div>
                        <div>
                            <label class="form-label">Additional Fee Notes</label>
                            <p class="text-xs text-gray-400 mb-1">One item per line (e.g. includes, discounts)</p>
                            <textarea name="course_fee_notes" rows="3" class="form-textarea mt-1 font-mono text-sm"
                                placeholder="Includes Puppy Owner's Guide and a treat bag&#10;25% discount for simultaneous enrolment/s (excluding enrolment fee)">{{ old('course_fee_notes', implode("\n", $class->course_fee_notes ?? [])) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Fees Section Photo</label>
                            <div class="grid grid-cols-2 gap-3 mt-1">
                                <div>
                                    <p class="text-xs text-gray-400 mb-1">Desktop</p>
                                    @if($class->fees_image_path)
                                    <img src="{{ Storage::url($class->fees_image_path) }}" class="w-full h-20 object-cover rounded-lg mb-2">
                                    @endif
                                    <input type="file" name="fees_image" accept="image/*" class="form-input">
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 mb-1">Mobile <span class="text-gray-300">(optional)</span></p>
                                    @if($class->fees_image_mobile_path)
                                    <img src="{{ Storage::url($class->fees_image_mobile_path) }}" class="w-full h-20 object-cover rounded-lg mb-2">
                                    @else
                                    <div class="w-full h-20 rounded-lg mb-2 bg-gray-50 border border-dashed border-gray-200 flex items-center justify-center">
                                        <span class="text-xs text-gray-300">Uses desktop if empty</span>
                                    </div>
                                    @endif
                                    <input type="file" name="fees_image_mobile" accept="image/*" class="form-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Bank Details</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name', $class->bank_name) }}" class="form-input mt-1" placeholder="FNB">
                        </div>
                        <div>
                            <label class="form-label">Account Name</label>
                            <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $class->bank_account_name) }}" class="form-input mt-1" placeholder="McKaynine Honeydew">
                        </div>
                        <div>
                            <label class="form-label">Account Number</label>
                            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $class->bank_account_number) }}" class="form-input mt-1">
                        </div>
                        <div>
                            <label class="form-label">Branch Code</label>
                            <input type="text" name="bank_branch_code" value="{{ old('bank_branch_code', $class->bank_branch_code) }}" class="form-input mt-1">
                        </div>
                        <div>
                            <label class="form-label">Payment Reference Note</label>
                            <input type="text" name="bank_reference_note" value="{{ old('bank_reference_note', $class->bank_reference_note) }}" class="form-input mt-1" placeholder="Please use your name as reference">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Contact Details</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Phone</label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $class->contact_phone) }}" class="form-input mt-1" placeholder="082 565 6160">
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $class->contact_email) }}" class="form-input mt-1" placeholder="honeydew@mckaynine.co.za">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right columns --}}
            <div class="lg:col-span-2 space-y-4">

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Hero Image</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Desktop</p>
                            @if($class->info_hero_image_path)
                            <img src="{{ Storage::url($class->info_hero_image_path) }}" class="w-full h-32 object-cover rounded-lg mb-2">
                            @endif
                            <input type="file" name="hero_image" accept="image/*" class="form-input">
                            <p class="text-xs text-gray-400 mt-1">Wide landscape, min 1200×500px</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Mobile <span class="text-gray-300 font-normal">(optional)</span></p>
                            @if($class->info_hero_image_mobile_path)
                            <img src="{{ Storage::url($class->info_hero_image_mobile_path) }}" class="w-full h-32 object-cover rounded-lg mb-2">
                            @else
                            <div class="w-full h-32 rounded-lg mb-2 bg-gray-50 border border-dashed border-gray-200 flex items-center justify-center">
                                <span class="text-xs text-gray-300">Uses desktop if empty</span>
                            </div>
                            @endif
                            <input type="file" name="hero_image_mobile" accept="image/*" class="form-input">
                            <p class="text-xs text-gray-400 mt-1">Portrait, min 600×700px</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Header Text</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Tagline (under class name)</label>
                            <input type="text" name="info_tagline" value="{{ old('info_tagline', $class->info_tagline) }}"
                                class="form-input mt-1" placeholder="Build calmness, confidence and great habits from day one">
                        </div>
                        <div>
                            <label class="form-label">Address (displayed on page)</label>
                            <input type="text" name="info_address" value="{{ old('info_address', $class->info_address ?? $class->location) }}"
                                class="form-input mt-1" placeholder="14 Cypress Road, Zonnehoewe, Honeydew">
                            <p class="text-xs text-gray-400 mt-1">Session dates & times pull from the class schedule automatically</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">What We Help With</h2>
                    <label class="form-label">One item per line</label>
                    <textarea name="info_helps_with" rows="7" class="form-textarea mt-1 font-mono text-sm"
                        placeholder="Nipping&#10;Chewing&#10;Social Manners&#10;Lead Walking&#10;Confidence&#10;Toilet Training">{{ old('info_helps_with', implode("\n", $class->info_helps_with ?? [])) }}</textarea>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">When Can I Start?</h2>
                    <textarea name="info_age_requirements" rows="4" class="form-textarea mt-1"
                        placeholder="Puppies should start at 10 to 14 weeks old...">{{ old('info_age_requirements', $class->info_age_requirements) }}</textarea>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Testimonial</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Photo</label>
                            @if($class->testimonial_photo_path)
                            <img src="{{ Storage::url($class->testimonial_photo_path) }}" class="w-16 h-16 rounded-full object-cover mb-2">
                            @endif
                            <input type="file" name="testimonial_photo" accept="image/*" class="form-input mt-1">
                        </div>
                        <div>
                            <label class="form-label">Quote</label>
                            <textarea name="testimonial_text" rows="3" class="form-textarea mt-1"
                                placeholder="From puppy class onwards, the team at McKaynine has gone out of their way to help...">{{ old('testimonial_text', $class->testimonial_text) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Name / Caption</label>
                            <input type="text" name="testimonial_name" value="{{ old('testimonial_name', $class->testimonial_name) }}"
                                class="form-input mt-1" placeholder="Megan with Lucy">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">What to Bring to Class</h2>
                    <label class="form-label">One item per line</label>
                    <textarea name="info_what_to_bring" rows="6" class="form-textarea mt-1 font-mono text-sm"
                        placeholder="Puppy wearing a normal flat buckle collar&#10;Light webbing lead (no chain or extendable leads)&#10;LOTS of small, soft treats&#10;Towel or mat for your puppy to lie on">{{ old('info_what_to_bring', implode("\n", $class->info_what_to_bring ?? [])) }}</textarea>
                </div>

                <div class="card">
                    <h2 class="font-semibold text-navy mb-4">Joining Notes</h2>
                    <p class="text-xs text-gray-400 mb-2">Extra bullet points shown below the joining steps. One per line.</p>
                    <textarea name="info_joining_notes" rows="3" class="form-textarea font-mono text-sm"
                        placeholder="Cut-off for enrolment is one business day before your first lesson&#10;We will send you a booking confirmation once we have received your docs">{{ old('info_joining_notes', $class->info_joining_notes) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">Save Info Page</button>
                </div>

            </div>
        </div>
    </form>

</div>
</x-app-layout>
