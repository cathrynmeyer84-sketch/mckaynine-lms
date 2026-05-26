<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ExamResult;
use App\Services\MessageService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        $submitted = ExamResult::where('status', 'submitted')->with(['enrolment.handler', 'enrolment.dog', 'enrolment.dogClass'])->latest()->get();
        $released = ExamResult::where('status', 'released')->with(['enrolment.handler', 'enrolment.dog', 'enrolment.dogClass'])->latest()->take(20)->get();
        return view('admin.results.index', compact('submitted', 'released'));
    }

    public function show(ExamResult $examResult)
    {
        $examResult->load([
            'enrolment.handler',
            'enrolment.dog',
            'enrolment.dogClass.classType.gradingExercises.deductionEvents',
            'enrolment.dogClass.classType.gradingExercises.ratingScales',
        ]);
        return view('admin.results.show', compact('examResult'));
    }

    public function edit(ExamResult $examResult)
    {
        $examResult->load([
            'enrolment.handler',
            'enrolment.dog',
            'enrolment.dogClass.classType.gradingExercises.deductionEvents',
            'enrolment.dogClass.classType.gradingExercises.ratingScales',
        ]);
        return view('admin.results.edit', compact('examResult'));
    }

    public function update(Request $request, ExamResult $examResult)
    {
        $request->validate([
            'total_score'         => 'required|numeric|min:0|max:100',
            'achievement_level'   => 'required|in:merit_pass,pass,review,fail',
            'instructor_comments' => 'nullable|string',
            'has_blocking_fault'  => 'boolean',
        ]);

        $examResult->update([
            'total_score'         => $request->total_score,
            'achievement_level'   => $request->achievement_level,
            'instructor_comments' => $request->instructor_comments,
            'has_blocking_fault'  => $request->boolean('has_blocking_fault'),
        ]);

        return redirect()->route('admin.results.show', $examResult)->with('success', 'Result updated.');
    }

    public function release(ExamResult $examResult)
    {
        $examResult->update(['status' => 'released', 'released_at' => now()]);

        $examResult->load(['enrolment.handler.user', 'enrolment.dog', 'enrolment.dogClass.instructors']);
        $enrolment = $examResult->enrolment;

        if ($enrolment?->handler?->user) {
            $slug = match($examResult->achievement_level) {
                'merit_pass' => 'completion_merit',
                'pass'       => 'completion_pass',
                'review'     => 'completion_review',
                'fail'       => 'completion_fail',
                default      => 'result_released',
            };

            app(MessageService::class)->sendTemplateToHandler(
                $slug,
                $enrolment->handler->user,
                ['handler' => $enrolment->handler, 'dog' => $enrolment->dog, 'class' => $enrolment->dogClass, 'enrolment' => $enrolment],
                auth()->id(),
                $enrolment->class_id
            );
        }

        return redirect()->route('admin.results.show', $examResult)->with('success', 'Result released to handler.');
    }
}
