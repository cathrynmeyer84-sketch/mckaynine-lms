@extends('layouts.app')

@section('title', 'Class Feedback')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Class Feedback</h1>
        <p class="page-subtitle">{{ $enrolment->dogClass->name }}</p>
    </div>
    <a href="{{ route('handler.classes.show', $enrolment) }}" class="btn btn-outline">← Back</a>
</div>

<div class="page-content">
    @if($existing)
    <div class="card mb-6 bg-green-50 border border-green-200">
        <p class="text-green-700 font-medium">Thank you! Your feedback has been submitted.</p>
    </div>
    @else
    <form action="{{ route('handler.survey.store', $enrolment) }}" method="POST" class="space-y-6">
        @csrf

        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Rate Your Experience</h2>

            <div class="mb-5">
                <label class="form-label">Overall Class Rating</label>
                <div class="flex gap-2" x-data="{ rating: {{ old('overall_rating', 0) }} }">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" @click="rating = {{ $i }}" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'" class="text-3xl focus:outline-none transition">★</button>
                    @endfor
                    <input type="hidden" name="overall_rating" x-bind:value="rating">
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label">Instructor Rating</label>
                <div class="flex gap-2" x-data="{ rating: {{ old('instructor_rating', 0) }} }">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" @click="rating = {{ $i }}" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'" class="text-3xl focus:outline-none transition">★</button>
                    @endfor
                    <input type="hidden" name="instructor_rating" x-bind:value="rating">
                </div>
            </div>

            <div>
                <label class="form-label">How likely are you to recommend McKaynine? (1–10)</label>
                <input type="range" name="likelihood_to_recommend" min="1" max="10" value="{{ old('likelihood_to_recommend', 7) }}" class="w-full" x-data x-model.number="val" x-ref="range">
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>1 – Not likely</span>
                    <span>10 – Definitely</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-base font-semibold text-navy mb-4">Tell Us More</h2>

            <div class="mb-4">
                <label class="form-label">What was most valuable about the class?</label>
                <textarea name="most_valuable" rows="3" class="form-textarea">{{ old('most_valuable') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Any suggestions for improvement?</label>
                <textarea name="suggestions" rows="3" class="form-textarea">{{ old('suggestions') }}</textarea>
            </div>

            <div>
                <label class="form-label">Any other comments?</label>
                <textarea name="comments" rows="3" class="form-textarea">{{ old('comments') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-full">Submit Feedback</button>
    </form>
    @endif
</div>
@endsection
