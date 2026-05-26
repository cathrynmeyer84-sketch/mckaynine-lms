<x-app-layout :title="'Resources'">
<div class="page-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Resources</h1>
            <p class="page-subtitle">Manage training resources and articles</p>
        </div>
        <a href="{{ route('admin.resources.create') }}" class="btn-primary">+ Add Resource</a>
    </div>

    @if($resources->count())
    <div class="card">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Visibility</th>
                        <th>Order</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                    <tr>
                        <td>
                            <div class="font-medium text-gray-900">{{ $resource->title }}</div>
                            @if($resource->class_categories)
                            @php $cats = is_array($resource->class_categories) ? $resource->class_categories : json_decode($resource->class_categories, true); @endphp
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach(($cats ?? []) as $cat)
                                    <span class="text-xs bg-brand/10 text-brand px-1.5 py-0.5 rounded">{{ ucwords(str_replace('_', ' ', $cat)) }}</span>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="text-sm text-gray-600">{{ $resource->category ?? '—' }}</td>
                        <td class="text-sm text-gray-600">
                            @if($resource->external_url)
                                <span class="flex items-center gap-1 text-brand">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Link
                                </span>
                            @elseif($resource->content)
                                Article
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.resources.toggle', $resource) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-1.5 text-sm {{ $resource->is_published ? 'text-green-600' : 'text-gray-400' }} hover:opacity-75">
                                    <div class="w-8 h-4 rounded-full transition-colors {{ $resource->is_published ? 'bg-green-500' : 'bg-gray-300' }} relative flex-shrink-0">
                                        <div class="absolute top-0.5 {{ $resource->is_published ? 'right-0.5' : 'left-0.5' }} w-3 h-3 bg-white rounded-full shadow transition-all"></div>
                                    </div>
                                    {{ $resource->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-sm text-gray-500">{{ $resource->sort_order ?? 0 }}</td>
                        <td>
                            <a href="{{ route('admin.resources.edit', $resource) }}" class="btn-outline btn-sm">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p>No resources yet</p>
            <a href="{{ route('admin.resources.create') }}" class="btn-primary btn-sm mt-3">Create first resource</a>
        </div>
    </div>
    @endif

</div>
</x-app-layout>
