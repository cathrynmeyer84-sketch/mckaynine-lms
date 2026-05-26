@extends('layouts.app')

@section('title', 'Enrolment Request Received')

@section('content')
<div class="page-content max-w-xl mx-auto py-16 px-4 text-center">
    <div class="mb-6 flex justify-center">
        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
    </div>

    <h1 class="text-2xl font-bold text-navy mb-3">Thank you!</h1>

    <p class="text-gray-600 mb-4">
        Thank you for enrolling <strong>{{ $dogName }}</strong> in <strong>{{ $className }}</strong>.
    </p>

    <p class="text-gray-500 text-sm mb-8">
        Our admin team will be in touch if they have any questions. Once your place has been confirmed, we'll send you a message with all the details.
    </p>

    <a href="{{ route('handler.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
</div>
@endsection
