@extends('layouts.app')

@section('title', 'Enrol at McKaynine')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[80vh] px-4 py-12">
    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-amber rounded-2xl flex items-center justify-center mx-auto mb-4 shadow">
                <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-navy">Welcome to McKaynine</h1>
            <p class="text-gray-500 mt-2 text-sm">Let's find the right programme for your pup.</p>
        </div>

        <div class="card"
            x-data="{
                dob: '',
                get ageMonths() {
                    if (!this.dob) return null;
                    const today = new Date();
                    const birth = new Date(this.dob);
                    if (birth > today) return null;
                    return (today.getFullYear() - birth.getFullYear()) * 12 +
                           (today.getMonth() - birth.getMonth());
                },
                get isPuppy() {
                    return this.ageMonths !== null && this.ageMonths < 4;
                },
                get redirectUrl() {
                    const path = this.isPuppy ? '/enrol/puppy' : '/enrol/assessment';
                    let url = path + '?dob=' + this.dob;
                    @if($classId) url += '&class_id={{ $classId }}'; @endif
                    return url;
                }
            }">

            <label class="form-label font-semibold">What is your pup's date of birth?</label>
            <input
                type="date"
                x-model="dob"
                :max="new Date().toISOString().split('T')[0]"
                class="form-input mt-2"
            >

            <div x-show="ageMonths !== null" x-cloak class="mt-5 space-y-4">

                <div x-show="isPuppy" class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800">
                    Your pup is eligible for our <strong>Puppy Class</strong> — fill in the enrolment form and we'll confirm a spot.
                </div>

                <div x-show="!isPuppy" class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
                    Your dog will start with an <strong>Assessment</strong> so we can find the best programme for them.
                </div>

                <a :href="redirectUrl" class="btn btn-primary w-full block text-center">
                    Continue →
                </a>

            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Already have an account?
            <a href="{{ route('login') }}" class="text-brand hover:underline">Sign in</a>
        </p>

    </div>
</div>
@endsection
