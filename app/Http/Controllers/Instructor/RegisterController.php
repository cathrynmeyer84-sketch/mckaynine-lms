<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DogClass;
use App\Models\ClassDate;
use App\Models\Register;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function show(DogClass $class, ClassDate $classDate)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $enrolments = $class->enrolments()
            ->where('status', 'confirmed')
            ->with(['dog', 'handler.user'])
            ->get();

        $registers = $classDate->registers()->get()->keyBy('enrolment_id');

        return view('instructor.register', compact('class', 'classDate', 'enrolments', 'registers'));
    }

    public function store(Request $request, DogClass $class, ClassDate $classDate)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $data = $request->validate([
            'attendance'   => 'array',
            'attendance.*' => 'nullable|in:present,absent',
            'notes'        => 'array',
            'notes.*'      => 'nullable|string|max:1000',
        ]);

        foreach ($class->enrolments()->where('status', 'confirmed')->pluck('id') as $enrolmentId) {
            Register::updateOrCreate(
                ['enrolment_id' => $enrolmentId, 'class_date_id' => $classDate->id],
                [
                    'attendance' => $data['attendance'][$enrolmentId] ?? 'absent',
                    'notes'      => $data['notes'][$enrolmentId] ?? null,
                    'marked_by'  => auth()->id(),
                    'marked_at'  => now(),
                ]
            );
        }

        return redirect()->route('instructor.classes.show', $class)->with('success', 'Register saved.');
    }
}
