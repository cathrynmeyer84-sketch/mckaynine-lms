<div class="card flex items-center gap-4">
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3 mb-1">
            <a href="{{ route('admin.class-types.show', $type) }}" class="font-semibold text-navy hover:text-brand">{{ $type->name }}</a>
            <span class="badge {{ $type->duration_type === 'term' ? 'badge-upcoming' : 'badge-active' }}">
                {{ $type->duration_label }}
            </span>
            @if($type->is_entry_class)
            <span class="badge badge-pending text-xs">Entry class</span>
            @endif
        </div>
        @if($type->description)
        <p class="text-sm text-gray-500 truncate">{{ $type->description }}</p>
        @endif
        <p class="text-xs text-gray-400 mt-1">
            {{ $type->classes_count }} {{ Str::plural('class', $type->classes_count) }} using this type
            @if($type->has_structured_content && $type->weeks->count())
            · {{ $type->weeks->count() }} weeks of content
            @endif
        </p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('admin.class-types.edit', $type) }}" class="btn btn-outline btn-sm">Edit</a>
        <form action="{{ route('admin.class-types.destroy', $type) }}" method="POST"
            onsubmit="return confirm('Delete {{ $type->name }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm text-red-500 border-red-200 hover:bg-red-50">Delete</button>
        </form>
    </div>
</div>
