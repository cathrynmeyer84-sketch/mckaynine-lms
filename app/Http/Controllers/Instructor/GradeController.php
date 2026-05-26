<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DogClass;
use App\Models\Enrolment;
use App\Models\CgcBronzeGrade;
use App\Models\EoGrade;
use App\Models\ExamResult;
use App\Models\GradingExercise;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(DogClass $class)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $class->load(['classType.gradingExercises.deductionEvents', 'classType.gradingExercises.ratingScales']);

        $enrolments = $class->enrolments()
            ->where('status', 'confirmed')
            ->with(['dog', 'handler', 'examResult'])
            ->get();

        $exercises = $class->classType?->gradingExercises ?? collect();

        return view('instructor.grade.index', compact('class', 'enrolments', 'exercises'));
    }

    public function exerciseView(DogClass $class, GradingExercise $exercise)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $exercise->load(['deductionEvents', 'ratingScales']);

        $enrolments = $class->enrolments()
            ->where('status', 'confirmed')
            ->with(['dog', 'handler', 'examResult'])
            ->get();

        return view('instructor.grade.exercise', compact('class', 'exercise', 'enrolments'));
    }

    public function saveExercise(Request $request, DogClass $class, GradingExercise $exercise, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $score = (float) $request->input('score', 0);

        $exerciseData = ['score' => $score];
        if ($exercise->type === 'rating') {
            $exerciseData['label']     = $request->input('label');
            $exerciseData['deduction'] = (float) $request->input('deduction', 0);
            $exerciseData['auto_fail'] = $request->boolean('auto_fail');
        } elseif ($exercise->type === 'time') {
            $exerciseData['time1'] = (float) $request->input('time1', 0);
            $exerciseData['time2'] = (float) $request->input('time2', 0);
        }

        $result = ExamResult::firstOrNew(['enrolment_id' => $enrolment->id]);
        $scores = $result->exercise_scores ?? [];
        $scores[$exercise->id] = $exerciseData;
        $result->exercise_scores = $scores;
        $result->graded_by  = $instructor->id;
        $result->exam_type  = 'class_type';
        $result->status     = $result->status ?? 'draft';

        // Recalculate total score as percentage of max marks
        $allExercises = $class->classType?->gradingExercises ?? collect();
        $maxTotal = $allExercises->sum(fn($e) => (float)($e->starting_marks ?? 0));
        $totalScore = collect($scores)->sum(fn($s) => $s['score'] ?? 0);
        $hasAutoFail = collect($scores)->contains(fn($s) => $s['auto_fail'] ?? false);

        $percentage = $maxTotal > 0 ? ($totalScore / $maxTotal) * 100 : 0;
        $result->total_score = round($percentage, 2);
        $result->has_blocking_fault = $hasAutoFail;
        $result->achievement_level = $hasAutoFail ? 'fail' : match(true) {
            $percentage >= 91 => 'merit_pass',
            $percentage >= 80 => 'pass',
            $percentage >= 70 => 'review',
            default           => 'fail',
        };

        $result->save();

        return back()->with('success', 'Score saved.');
    }

    public function form(DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);
        $enrolment->load(['dog', 'handler.user', 'examResult']);
        $class->load(['classType.gradingExercises.deductionEvents', 'classType.gradingExercises.ratingScales']);
        $exercises = $class->classType?->gradingExercises ?? collect();
        return view('instructor.grade.form', compact('class', 'enrolment', 'exercises'));
    }

    public function store(Request $request, DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $data = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'instructor_notes' => 'nullable|string',
            'achievement_level' => 'nullable|string',
        ]);

        $existing = ExamResult::where('enrolment_id', $enrolment->id)->first();
        $alreadySubmitted = in_array($existing?->status, ['submitted', 'released']);

        ExamResult::updateOrCreate(
            ['enrolment_id' => $enrolment->id],
            array_merge($data, [
                'graded_by'    => $instructor->id,
                'graded_at'    => now(),
                'status'       => $alreadySubmitted ? $existing->status : 'submitted',
                'submitted_at' => $alreadySubmitted ? $existing->submitted_at : now(),
            ])
        );

        return redirect()->route('instructor.grade.index', $class)->with('success', 'Grades submitted for admin review.');
    }

    public function cgcBronzeForm(DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);
        $enrolment->load(['dog', 'handler.user']);
        $grade = $enrolment->examResult?->cgcBronzeGrade ?? null;
        return view('instructor.grade.cgc-bronze', compact('class', 'enrolment', 'grade'));
    }

    public function storeCgcBronze(Request $request, DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $ratings = $request->validate([
            'ratings' => 'required|array',
            'ratings.*' => 'in:excellent,very_good,conditional,not_ready',
            'instructor_notes' => 'nullable|string',
        ]);

        $grade = new CgcBronzeGrade();
        $grade->enrolment_id = $enrolment->id;
        $grade->instructor_id = $instructor->id;
        $grade->fill($request->only(array_keys(CgcBronzeGrade::$deductions)));
        foreach ($request->input('ratings', []) as $key => $value) {
            $grade->$key = $value;
        }
        $grade->instructor_notes = $request->input('instructor_notes');
        $grade->calculateScore();
        $grade->save();

        $existing = ExamResult::where('enrolment_id', $enrolment->id)->first();
        $alreadySubmitted = in_array($existing?->status, ['submitted', 'released']);

        ExamResult::updateOrCreate(
            ['enrolment_id' => $enrolment->id],
            [
                'score'              => $grade->total_score,
                'achievement_level'  => $grade->achievement_level,
                'has_blocking_fault' => $grade->has_blocking_fault,
                'graded_by'          => $instructor->id,
                'graded_at'          => now(),
                'status'             => $alreadySubmitted ? $existing->status : 'submitted',
                'submitted_at'       => $alreadySubmitted ? $existing->submitted_at : now(),
            ]
        );

        return redirect()->route('instructor.grade.index', $class)->with('success', 'CGC Bronze grades submitted for admin review.');
    }

    public function eoForm(DogClass $class, Enrolment $enrolment)
    {
        return redirect()->route('instructor.grade.form', [$class, $enrolment]);
    }

    public function submitForReview(DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $result = ExamResult::where('enrolment_id', $enrolment->id)->firstOrFail();
        if ($result->status === 'draft') {
            $result->update(['status' => 'submitted', 'submitted_at' => now()]);
        }

        return redirect()->route('instructor.grade.index', $class)->with('success', 'Result submitted for admin review.');
    }

    public function storeEo(Request $request, DogClass $class, Enrolment $enrolment)
    {
        $instructor = auth()->user()->instructor;
        abort_unless($instructor && $instructor->classes()->where('class_id', $class->id)->exists(), 403);

        $request->validate([
            'ex_scores'      => 'required|array',
            'ex_scores.*'    => 'numeric|min:0',
            'global_comments' => 'nullable|string',
        ]);

        $rawScores   = $request->input('ex_scores', []);
        $totalScore  = array_sum($rawScores);
        $maxTotal    = (float) $request->input('max_total', 100);
        $percentage  = $maxTotal > 0 ? ($totalScore / $maxTotal) * 100 : 0;

        $achievementLevel = match(true) {
            $percentage >= 91 => 'merit_pass',
            $percentage >= 80 => 'pass',
            $percentage >= 70 => 'review',
            default           => 'fail',
        };

        $hasAutoFail = (bool) $request->input('has_auto_fail', false);

        // Build exercise_scores in the same format saveExercise uses
        $exerciseScores = [];
        foreach ($rawScores as $exId => $score) {
            $exerciseScores[$exId] = ['score' => (float) $score];
        }

        $existing = ExamResult::where('enrolment_id', $enrolment->id)->first();
        $alreadySubmitted = in_array($existing?->status, ['submitted', 'released']);

        ExamResult::updateOrCreate(
            ['enrolment_id' => $enrolment->id],
            [
                'graded_by'           => $instructor->id,
                'exam_type'           => 'class_type',
                'total_score'         => round($percentage, 2),
                'exercise_scores'     => $exerciseScores,
                'achievement_level'   => $hasAutoFail ? 'fail' : $achievementLevel,
                'has_blocking_fault'  => $hasAutoFail,
                'instructor_comments' => $request->input('global_comments'),
                'evaluator_name'      => $request->input('evaluator_name'),
                'exam_date'           => $request->input('exam_date'),
                'status'              => $alreadySubmitted ? $existing->status : 'submitted',
                'submitted_at'        => $alreadySubmitted ? $existing->submitted_at : now(),
            ]
        );

        return redirect()->route('instructor.grade.index', $class)->with('success', 'Grades submitted for admin review.');
    }
}
