@extends('layouts.app')

@section('title', 'Edit ' . $dog->name)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Edit {{ $dog->name }}</h1>
        <p class="page-subtitle">Update your dog's profile</p>
    </div>
    <a href="{{ route('handler.dogs.index') }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content">
    <form action="{{ route('handler.dogs.update', $dog) }}" method="POST" class="space-y-6"
          x-data="{
              photoPath: '{{ $dog->photo_path ?? '' }}',
              photoPreview: '{{ $dog->photo_path ? Storage::url($dog->photo_path) : '' }}',
              photoUploading: false,
              photoError: '',
              vaccinationPath: '{{ $dog->vaccination_card_path ?? '' }}',
              vaccinationUploaded: {{ $dog->vaccination_card_path ? 'true' : 'false' }},
              vaccinationUploading: false,
              vaccinationFileName: '',
              vaccinationError: '',
              async uploadPhoto(event) {
                  const file = event.target.files[0];
                  if (!file) return;
                  this.photoUploading = true;
                  this.photoError = '';
                  const fd = new FormData();
                  fd.append('file', file);
                  fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                  try {
                      const res = await fetch('{{ route('enrol.upload.dog-photo') }}', { method: 'POST', body: fd });
                      const text = await res.text();
                      if (!res.ok) {
                          if (res.status === 413) throw new Error('File too large — please try a smaller image.');
                          let msg = 'Upload failed — please try again.';
                          try { msg = JSON.parse(text)?.message || msg; } catch {}
                          throw new Error(msg);
                      }
                      const data = JSON.parse(text);
                      this.photoPath = data.path;
                      this.photoPreview = URL.createObjectURL(file);
                  } catch (e) {
                      this.photoError = e.message || 'Upload failed — please try again.';
                      this.photoPath = '{{ $dog->photo_path ?? '' }}';
                  } finally {
                      this.photoUploading = false;
                  }
              },
              async uploadVaccination(event) {
                  const file = event.target.files[0];
                  if (!file) return;
                  this.vaccinationUploading = true;
                  this.vaccinationUploaded = false;
                  this.vaccinationError = '';
                  this.vaccinationFileName = file.name;
                  const fd = new FormData();
                  fd.append('file', file);
                  fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                  try {
                      const res = await fetch('{{ route('enrol.upload.vaccination') }}', { method: 'POST', body: fd });
                      const text = await res.text();
                      if (!res.ok) {
                          if (res.status === 413) throw new Error('File too large — please compress and try again.');
                          let msg = 'Upload failed — please try again.';
                          try { msg = JSON.parse(text)?.message || msg; } catch {}
                          throw new Error(msg);
                      }
                      this.vaccinationPath = JSON.parse(text).path;
                      this.vaccinationUploaded = true;
                  } catch (e) {
                      this.vaccinationError = e.message || 'Upload failed — please try again.';
                      this.vaccinationPath = '{{ $dog->vaccination_card_path ?? '' }}';
                      this.vaccinationUploaded = {{ $dog->vaccination_card_path ? 'true' : 'false' }};
                  } finally {
                      this.vaccinationUploading = false;
                  }
              }
          }">
        @csrf
        @method('PATCH')
        <input type="hidden" name="photo_path" :value="photoPath">
        <input type="hidden" name="vaccination_card_path" :value="vaccinationPath">

        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Dog Information</h2>

            {{-- Photo --}}
            <div class="form-section mb-6">
                <label class="form-label">Photo</label>
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-stone/20 flex items-center justify-center flex-shrink-0">
                        <img x-show="photoPreview" :src="photoPreview" alt="{{ $dog->name }}" class="w-20 h-20 object-cover">
                        <svg x-show="!photoPreview" class="w-10 h-10 text-stone" fill="currentColor" viewBox="0 0 24 24"><path d="M4.5 11.5A6.5 6.5 0 0 1 11 5h2a6.5 6.5 0 0 1 6.5 6.5A4.5 4.5 0 0 1 15 16v1a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1v-1a4.5 4.5 0 0 1-4.5-4.5Z"/></svg>
                    </div>
                    <div>
                        <label class="btn btn-outline text-sm cursor-pointer">
                            <span x-text="photoUploading ? 'Uploading…' : 'Choose photo'"></span>
                            <input type="file" accept="image/*" class="sr-only" @change="uploadPhoto($event)" :disabled="photoUploading">
                        </label>
                        <p class="text-xs text-gray-400 mt-1">JPG or PNG, up to 10MB</p>
                        <p x-show="photoError" x-text="photoError" class="text-xs text-red-500 mt-1"></p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $dog->name) }}" class="form-input" required>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Breed</label>
                    <input type="text" name="breed" value="{{ old('breed', $dog->breed) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $dog->date_of_birth?->format('Y-m-d')) }}" class="form-input">
                    @error('date_of_birth')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Microchip Number</label>
                    <input type="text" name="microchip_number" value="{{ old('microchip_number', $dog->microchip_number) }}" class="form-input">
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Vaccination Details</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Vaccination Expiry Date</label>
                    <input type="date" name="vaccination_expiry" value="{{ old('vaccination_expiry', $dog->vaccination_expiry?->format('Y-m-d')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Vaccination Card</label>
                    <p x-show="vaccinationUploaded" class="text-sm text-green-600 mb-1">✓ Card uploaded</p>
                    <label class="btn btn-outline text-sm cursor-pointer inline-flex">
                        <span x-text="vaccinationUploading ? 'Uploading…' : (vaccinationUploaded ? 'Replace card' : 'Upload card')"></span>
                        <input type="file" accept="image/*,application/pdf" class="sr-only" @change="uploadVaccination($event)" :disabled="vaccinationUploading">
                    </label>
                    <p x-show="vaccinationFileName" x-text="vaccinationFileName" class="text-xs text-gray-500 mt-1"></p>
                    <p class="text-xs text-gray-400 mt-1">Photo or PDF of vaccination certificate</p>
                    <p x-show="vaccinationError" x-text="vaccinationError" class="text-xs text-red-500 mt-1"></p>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('handler.dogs.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
