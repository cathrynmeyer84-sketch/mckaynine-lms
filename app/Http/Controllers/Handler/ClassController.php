<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\Enrolment;
use App\Models\ClassDate;
use App\Models\Survey;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $handler = auth()->user()->handler;
        $enrolments = $handler->enrolments()
            ->with(['dogClass', 'dog', 'examResult'])
            ->orderByDesc('enrolled_at')
            ->get();
        return view('handler.classes.index', compact('enrolments'));
    }

    public function show(Enrolment $enrolment)
    {
        $handler = auth()->user()->handler;
        abort_unless($enrolment->handler_id === $handler->id, 403);

        $enrolment->load([
            'dogClass.classDates' => fn($q) => $q->orderBy('date'),
            'dogClass.classType.gradingExercises',
            'dog',
            'registers',
            'examResult',
            'goals' => fn($q) => $q->where('visible_to_handler', true)->where('status', 'active'),
        ]);

        return view('handler.classes.show', compact('enrolment'));
    }

    public function weekContent(Enrolment $enrolment, ClassDate $classDate)
    {
        $handler = auth()->user()->handler;
        abort_unless($enrolment->handler_id === $handler->id, 403);
        abort_unless($classDate->class_id === $enrolment->class_id, 404);
        abort_unless($classDate->isContentPublished(), 403, 'Content not yet available.');

        $classDate->load('weeklyContent');
        $attendance = $enrolment->registers()->where('class_date_id', $classDate->id)->first();

        return view('handler.classes.week', compact('enrolment', 'classDate', 'attendance'));
    }

    public function surveyForm(Enrolment $enrolment)
    {
        $handler = auth()->user()->handler;
        abort_unless($enrolment->handler_id === $handler->id, 403);
        abort_unless($enrolment->dogClass->status === 'completed', 403, 'Survey only available after class completion.');

        $existing = $enrolment->survey;
        return view('handler.survey', compact('enrolment', 'existing'));
    }

    public function storeSurvey(Request $request, Enrolment $enrolment)
    {
        $handler = auth()->user()->handler;
        abort_unless($enrolment->handler_id === $handler->id, 403);

        $data = $request->validate([
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'instructor_rating' => 'nullable|integer|min:1|max:5',
            'most_valuable' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|string|max:1000',
            'likelihood_to_recommend' => 'nullable|integer|min:1|max:10',
            'comments' => 'nullable|string|max:2000',
        ]);

        Survey::updateOrCreate(
            ['enrolment_id' => $enrolment->id],
            array_merge($data, [
                'handler_id' => $handler->id,
                'submitted_at' => now(),
            ])
        );

        return redirect()->route('handler.classes.show', $enrolment)->with('success', 'Thank you for your feedback!');
    }
}
