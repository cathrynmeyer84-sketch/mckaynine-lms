<x-app-layout :title="'Edit Result — ' . $examResult->enrolment->dog->name">
<div class="page-content">

    <div class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.results.show', $examResult) }}" class="text-gray-400 hover:text-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="page-title">Edit Result</h1>
                <p class="page-subtitle">{{ $examResult->enrolment->handler->full_name }} — {{ $examResult->enrolment->dog->name }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-4">

            {{-- Context --}}
            <div class="card bg-gray-50 border-0">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">{{ $examResult->enrolment->dogClass->name }}</span>
                    @if($examResult->evaluator_name) · Evaluated by {{ $examResult->evaluator_name }} @endif
                    @if($examResult->exam_date) on {{ $examResult->exam_date->format('d M Y') }} @endif
                </p>
            </div>

            <form method="POST" action="{{ route('admin.results.update', $examResult) }}">
                @csrf @method('PUT')

                <div class="card space-y-5">
                    <h2 class="form-section-title">Result Details</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Total Score (%)</label>
                            <input type="number" name="total_score" step="0.01" min="0" max="100"
                                value="{{ old('total_score', number_format($examResult->total_score, 2)) }}"
                                class="form-input w-full" required>
                            @error('total_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Achievement Level</label>
                            <select name="achievement_level" class="form-select w-full" required>
                                @foreach(['merit_pass' => 'Merit Pass', 'pass' => 'Pass', 'review' => 'Review', 'fail' => 'Fail'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('achievement_level', $examResult->achievement_level) === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('achievement_level')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="has_blocking_fault" name="has_blocking_fault" value="1"
                            class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-400"
                            @checked(old('has_blocking_fault', $examResult->has_blocking_fault))>
                        <label for="has_blocking_fault" class="text-sm font-medium text-gray-700">Auto-fail / blocking fault recorded</label>
                    </div>

                    <div>
                        <label class="form-label">Instructor Comments</label>
                        <textarea name="instructor_comments" rows="4" class="form-textarea w-full"
                            placeholder="Overall comments for the handler...">{{ old('instructor_comments', $examResult->instructor_comments) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <button type="submit" class="btn-primary flex-1">Save Changes</button>
                    <a href="{{ route('admin.results.show', $examResult) }}" class="btn-outline flex-1 text-center">Cancel</a>
                </div>
            </form>

        </div>

        <div class="space-y-4">

            {{-- Read-only exercise breakdown --}}
            @php
                $exercises = $examResult->enrolment->dogClass->classType?->gradingExercises ?? collect();
                $scores = $examResult->exercise_scores ?? [];
            @endphp

            @if($exercises->isNotEmpty())
            <div class="card">
                <h2 class="form-section-title">Exercise Scores</h2>
                <p class="text-xs text-gray-400 mb-3">Read-only — edit individual exercises from the grading view.</p>
                <div class="divide-y divide-gray-100">
                    @foreach($exercises as $ex)
                    @php $exScore = $scores[$ex->id] ?? null; @endphp
                    <div class="py-2.5 flex items-center justify-between gap-2">
                        <p class="text-xs text-gray-700 leading-tight">{{ $ex->name }}</p>
                        <span class="text-xs font-semibold text-navy shrink-0">
                            {{ $exScore !== null ? ($exScore['score'] ?? '—') . '/' . ($ex->starting_marks ?? '?') : '—' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
</x-app-layout>
