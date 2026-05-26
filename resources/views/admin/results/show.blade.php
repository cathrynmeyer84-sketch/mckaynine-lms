<x-app-layout :title="'Result — ' . $examResult->enrolment->dog->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.results.index') }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Score Sheet</h1>
                <p class="page-subtitle">{{ $examResult->enrolment->handler->full_name }} — {{ $examResult->enrolment->dog->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inbox.compose', ['handler_id' => $examResult->enrolment->handler_id]) }}" class="btn-outline btn-sm">Send Message</a>
            <a href="{{ route('admin.results.edit', $examResult) }}" class="btn-outline btn-sm">Edit Result</a>
            @if($examResult->status === 'submitted')
            <form method="POST" action="{{ route('admin.results.release', $examResult) }}">
                @csrf
                <button type="submit" class="btn-amber btn-sm">Release to Handler</button>
            </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-4">

            {{-- Summary card --}}
            <div class="card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Class</p>
                        <p class="font-semibold text-gray-900">{{ $examResult->enrolment->dogClass->name }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $statusClass = match($examResult->status) {
                                'released'  => 'badge-active',
                                'submitted' => 'bg-amber/10 text-amber badge',
                                default     => 'bg-gray-100 text-gray-600 badge',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} capitalize">{{ $examResult->status }}</span>
                    </div>
                </div>
                @if($examResult->evaluator_name || $examResult->exam_date)
                <div class="mt-3 pt-3 border-t border-gray-100 flex gap-6 text-sm text-gray-500">
                    @if($examResult->evaluator_name)
                    <span>Evaluator: <span class="font-medium text-gray-700">{{ $examResult->evaluator_name }}</span></span>
                    @endif
                    @if($examResult->exam_date)
                    <span>Date: <span class="font-medium text-gray-700">{{ $examResult->exam_date->format('d M Y') }}</span></span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Exercise breakdown --}}
            @php
                $exercises = $examResult->enrolment->dogClass->classType?->gradingExercises ?? collect();
                $scores = $examResult->exercise_scores ?? [];
            @endphp

            @if($exercises->isNotEmpty())
            <div class="card">
                <h2 class="form-section-title">Exercise Scores</h2>
                <div class="divide-y divide-gray-100">
                    @foreach($exercises as $ex)
                    @php $exScore = $scores[$ex->id] ?? null; @endphp
                    <div class="py-3 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $loop->iteration }}. {{ $ex->name }}</p>
                            @if($exScore)
                                @if($ex->type === 'rating' && isset($exScore['label']))
                                <p class="text-xs text-gray-500 mt-0.5">Rating: {{ $exScore['label'] }}
                                    @if($exScore['auto_fail'] ?? false)<span class="text-red-500 ml-1">⚠ Auto-fail</span>@endif
                                </p>
                                @elseif($ex->type === 'time')
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Time 1: {{ $exScore['time1'] ?? 0 }}s
                                    @if(isset($exScore['time2'])) · Time 2: {{ $exScore['time2'] }}s @endif
                                </p>
                                @endif
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if($exScore !== null)
                            <span class="font-semibold text-navy">{{ $exScore['score'] ?? '—' }}/{{ $ex->starting_marks ?? '?' }}</span>
                            @else
                            <span class="text-xs text-gray-400">Not graded</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Comments --}}
            @if($examResult->instructor_comments)
            <div class="card">
                <h2 class="form-section-title">Instructor Comments</h2>
                <p class="text-sm text-gray-700 leading-relaxed">{{ $examResult->instructor_comments }}</p>
            </div>
            @endif

        </div>

        <div class="space-y-4">

            {{-- Score summary --}}
            <div class="card text-center">
                <p class="text-5xl font-bold text-navy">{{ number_format($examResult->total_score, 1) }}<span class="text-2xl text-gray-400">%</span></p>
                @if($examResult->achievement_level)
                @php
                    $lvlClass = match($examResult->achievement_level) {
                        'merit_pass' => 'bg-green-100 text-green-800',
                        'pass'       => 'bg-blue-100 text-blue-800',
                        'review'     => 'bg-amber/20 text-amber',
                        default      => 'bg-red-100 text-red-700',
                    };
                @endphp
                <span class="inline-block mt-3 px-4 py-1.5 rounded-full text-sm font-semibold {{ $lvlClass }}">
                    {{ ucwords(str_replace('_', ' ', $examResult->achievement_level)) }}
                </span>
                @endif
                @if($examResult->has_blocking_fault)
                <p class="text-xs text-red-500 mt-2 font-medium">⚠ Auto-fail recorded</p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="card space-y-3">
                <a href="{{ route('admin.results.edit', $examResult) }}" class="btn-primary w-full block text-center">Edit Result</a>
                @if($examResult->status === 'submitted')
                <form method="POST" action="{{ route('admin.results.release', $examResult) }}">
                    @csrf
                    <button type="submit" class="btn-amber w-full">Release to Handler</button>
                </form>
                @elseif($examResult->status === 'released')
                <p class="text-xs text-center text-green-600 font-medium">Released {{ $examResult->released_at?->format('d M Y') }}</p>
                @endif
            </div>

        </div>
    </div>

</div>
</x-app-layout>
