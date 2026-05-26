<x-app-layout title="Compose Message">
<div class="page-content max-w-2xl">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inbox.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="page-title">Compose Message</h1>
        </div>
    </div>

    <div class="card" x-data="{ type: 'handler' }">
        <form method="POST" action="{{ route('admin.inbox.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="form-label">Send to</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['handler' => 'A Handler', 'instructor' => 'An Instructor', 'class' => 'A Class (all students)', 'school' => 'All Active Students'] as $val => $label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="recipient_type" value="{{ $val }}" x-model="type" class="text-brand"
                            {{ $val === 'handler' ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div x-show="type === 'handler'">
                <label class="form-label">Handler</label>
                <select name="handler_user_id" class="form-select w-full">
                    <option value="">— Select handler —</option>
                    @foreach($handlers as $h)
                    <option value="{{ $h->user_id }}" {{ request('handler_id') == $h->id ? 'selected' : '' }}>
                        {{ $h->first_name }} {{ $h->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div x-show="type === 'instructor'">
                <label class="form-label">Instructor</label>
                <select name="instructor_user_id" class="form-select w-full">
                    <option value="">— Select instructor —</option>
                    @foreach($instructors as $i)
                    <option value="{{ $i->user_id }}">{{ $i->first_name }} {{ $i->last_name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="type === 'class'">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-select w-full">
                    <option value="">— Select class —</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" class="form-input w-full" required>
            </div>

            <div>
                <label class="form-label">Message</label>
                <textarea name="body" rows="8" class="form-textarea w-full" required>{{ old('body') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">You can use merge tags: {{handler_name}}, {{dog_name}}, {{class_name}}</p>
            </div>

            <button type="submit" class="btn-primary w-full">Send Message</button>
        </form>
    </div>

</div>
</x-app-layout>
