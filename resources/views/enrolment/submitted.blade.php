@extends('layouts.app')

@section('title', 'Enrolment Submitted')

@section('content')
<div class="page-content max-w-xl mx-auto">
    <div class="card text-center py-12">
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-navy mb-3">All Done!</h1>
        <p class="text-gray-600">
            @if(session('assessment_booked'))
                Thank you for booking your assessment! You should receive a confirmation email shortly with all the important info. Looking forward to meeting you and your pup.
            @elseif(session('graduate_enrolled'))
                Thank you! We have received your enrolment. Our team will be in touch shortly to confirm your class placement.
            @else
                Thank you for submitting your enrolment. Our admin team will be in touch if we have any questions.
            @endif
        </p>
    </div>
</div>
@endsection
