<x-app-layout title="Resources">
<div class="page-content" x-data="resourceSearch()" x-init="init()">

    <div class="page-header">
        <div>
            <h1 class="page-title">Resources</h1>
            <p class="page-subtitle">Guides and articles from the McKaynine team</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-4">
        <div class="flex gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input
                    type="text"
                    x-model="query"
                    @input="syncUrl()"
                    placeholder="Search articles…"
                    class="form-input w-full pl-9"
                    autocomplete="off"
                >
            </div>
            <button
                x-show="query || activeCategory"
                x-cloak
                @click="query = ''; activeCategory = ''; syncUrl()"
                class="btn-outline btn-sm self-center whitespace-nowrap"
            >Clear</button>
        </div>

        {{-- Category chips --}}
        <div class="flex flex-wrap gap-2 mt-3">
            <button
                @click="activeCategory = ''; syncUrl()"
                :class="activeCategory === '' ? 'bg-navy text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                class="px-3 py-1 rounded-full text-xs font-medium transition-colors"
            >All</button>
            @foreach($categories as $cat)
            <button
                @click="activeCategory = activeCategory === '{{ $cat }}' ? '' : '{{ $cat }}'; syncUrl()"
                :class="activeCategory === '{{ $cat }}' ? 'bg-navy text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                class="px-3 py-1 rounded-full text-xs font-medium transition-colors"
            >{{ $cat }}</button>
            @endforeach
        </div>
    </div>

    {{-- Article list --}}
    <div class="card overflow-hidden divide-y divide-gray-100">
        @forelse($resources as $resource)
        <a
            href="{{ route('handler.resources.show', $resource) }}"
            x-show="matches({{ json_encode(['title' => $resource->title, 'category' => $resource->category, 'content' => strip_tags($resource->content ?? '')]) }})"
            x-cloak
            class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors group"
        >
            {{-- Optional image thumbnail --}}
            @if($resource->image_path)
            <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                <img src="{{ Storage::url($resource->image_path) }}" alt="" class="w-full h-full object-cover">
            </div>
            @else
            <div class="w-10 h-10 rounded-lg bg-navy/8 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-navy/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            @endif

            <div class="flex-1 min-w-0">
                <p class="font-semibold text-navy text-sm leading-snug group-hover:text-brand transition-colors">{{ $resource->title }}</p>
                @if($resource->category)
                <p class="text-xs text-gray-400 mt-0.5">{{ $resource->category }}</p>
                @endif
            </div>

            <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-400 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @empty
        <div class="empty-state py-12">
            <div class="empty-state-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p>No resources available yet.</p>
        </div>
        @endforelse
    </div>

    {{-- No search results --}}
    <div x-show="visibleCount() === 0 && {{ $resources->count() }} > 0" x-cloak class="card text-center py-10 mt-4">
        <p class="text-gray-500 text-sm">No articles match your search.</p>
        <button @click="query = ''; activeCategory = ''; syncUrl()" class="btn-outline btn-sm mt-3">Clear filters</button>
    </div>

</div>

<script>
function resourceSearch() {
    return {
        query: '',
        activeCategory: '',

        init() {
            const params = new URLSearchParams(window.location.search);
            this.query = params.get('q') || '';
            this.activeCategory = params.get('cat') || '';
        },

        syncUrl() {
            const params = new URLSearchParams();
            if (this.query) params.set('q', this.query);
            if (this.activeCategory) params.set('cat', this.activeCategory);
            const qs = params.toString();
            history.replaceState(null, '', qs ? '?' + qs : window.location.pathname);
        },

        matches(article) {
            const q = this.query.toLowerCase().trim();
            const catMatch = !this.activeCategory || article.category === this.activeCategory;
            if (!catMatch) return false;
            if (!q) return true;
            return article.title.toLowerCase().includes(q)
                || (article.category || '').toLowerCase().includes(q)
                || (article.content || '').toLowerCase().includes(q);
        },

        visibleCount() {
            return document.querySelectorAll('a[x-show]:not([style*="display: none"])').length;
        }
    }
}
</script>
</x-app-layout>
