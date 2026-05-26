<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ClassDate;
use App\Models\DogClass;
use App\Models\Enrolment;
use App\Models\HandlerGoal;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $instructor = auth()->user()->instructor;
        $today = now()->startOfDay();
        $classes = $instructor->classes()
            ->with(['enrolments' => fn($q) => $q->where('status', 'confirmed')])
            ->orderByRaw("CASE
                WHEN start_date <= ? AND end_date >= ? THEN 0
                WHEN start_date > ? THEN 1
                ELSE 2
            END", [$today, $today, $today])
            ->orderBy('start_date')
            ->get();
        return view('instructor.classes.index', compact('classes'));
    }

    public function show(DogClass $class)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $class->load([
            'enrolments' => fn($q) => $q->where('status', 'confirmed')
                ->with(['dog', 'handler', 'goals' => fn($gq) => $gq->where('status', 'active'), 'examResult']),
            'classDates' => fn($q) => $q->orderBy('date')->withCount('registers')->with(['weeklyContent', 'classTypeWeek.briefingItems']),
            'instructors',
        ]);

        $goals = HandlerGoal::whereHas('enrolment', fn($q) => $q->where('class_id', $class->id))
            ->where('instructor_id', $instructor->id)
            ->with(['enrolment.handler', 'enrolment.dog'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('instructor.classes.show', compact('class', 'instructor', 'goals'));
    }

    public function showDog(DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $enrolment->load([
            'dog',
            'handler',
            'goals' => fn($q) => $q->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'achieved' THEN 1 ELSE 2 END"),
            'registers.classDate',
        ]);

        return view('instructor.classes.dog', compact('class', 'enrolment', 'instructor'));
    }

    public function showWeekBriefing(DogClass $class, ClassDate $classDate)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);
        $classDate->load(['classTypeWeek.briefingItems']);
        return view('instructor.classes.week-briefing', compact('class', 'classDate'));
    }

    public function showWeekContent(DogClass $class, ClassDate $classDate)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);
        $classDate->load(['weeklyContent', 'classTypeWeek']);
        return view('instructor.classes.week-content', compact('class', 'classDate'));
    }

    public function storeGoal(Request $request, DogClass $class)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $data = $request->validate([
            'enrolment_id' => 'required|exists:enrolments,id',
            'goal' => 'required|string|max:1000',
            'visible_to_handler' => 'boolean',
        ]);

        HandlerGoal::create([
            'enrolment_id' => $data['enrolment_id'],
            'instructor_id' => $instructor->id,
            'goal' => $data['goal'],
            'visible_to_handler' => $request->boolean('visible_to_handler'),
            'status' => 'active',
        ]);

        return back()->with('success', 'Goal added.');
    }

    public function updateGoal(Request $request, HandlerGoal $goal)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($goal->instructor_id === $instructor->id, 403);

        $data = $request->validate([
            'progress_notes' => 'nullable|string',
            'status' => 'in:active,achieved,dropped',
            'visible_to_handler' => 'boolean',
        ]);

        $goal->update($data);
        return back()->with('success', 'Goal updated.');
    }
}
