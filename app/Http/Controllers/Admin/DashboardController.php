<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Handler, Enrolment, AssessmentRequest, AssessmentSlot, DogClass, ExamResult, PrivateLesson};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingClassConfirmations = Enrolment::where('status', 'pending')->where('pathway', 'existing')->with(['handler', 'dog', 'dogClass'])->latest()->get();
        $pendingEnrolments = Enrolment::where('status', 'pending')
            ->where(fn($q) => $q->where('pathway', '!=', 'existing')->orWhereNull('pathway'))
            ->with(['handler', 'dog', 'dogClass'])->latest()->get();
        $pendingAssessments = AssessmentRequest::where('status', 'pending')->with(['handler', 'dog'])->latest()->get();
        $upcomingAssessments = AssessmentRequest::where('status', 'booked')
            ->whereHas('slot', fn($q) => $q->where('date', '>=', today()))
            ->with(['handler', 'dog', 'slot'])
            ->join('assessment_slots', 'assessment_requests.assessment_slot_id', '=', 'assessment_slots.id')
            ->orderBy('assessment_slots.date')
            ->orderBy('assessment_slots.start_time')
            ->select('assessment_requests.*')
            ->get();
        $today = now()->startOfDay();
        $upcomingClasses = DogClass::where('start_date', '>', $today)->with(['instructors', 'dates'])->get();
        $activeClasses = DogClass::where('start_date', '<=', $today)->where('end_date', '>=', $today)->with(['instructors', 'confirmedEnrolments'])->get();
        $pendingResults = ExamResult::where('status', 'submitted')->with(['enrolment.handler', 'enrolment.dog', 'enrolment.dogClass'])->get();
        $pendingLessons = PrivateLesson::where('status', 'pending')
            ->with(['handler', 'dog', 'instructor'])
            ->latest()->get();
        $upcomingLessons = PrivateLesson::where('status', 'confirmed')
            ->whereDate('requested_date', '>=', Carbon::today())
            ->with(['handler', 'dog', 'instructor'])
            ->orderBy('requested_date')->orderBy('requested_start_time')
            ->get();

        $stats = [
            'total_handlers'      => Handler::where('status', 'active')->count(),
            'pending_enrolments'  => $pendingEnrolments->count() + $pendingClassConfirmations->count(),
            'pending_assessments' => $pendingAssessments->count(),
            'active_classes'      => $activeClasses->count(),
            'pending_lessons'     => $pendingLessons->count(),
        ];
        return view('admin.dashboard', compact('pendingEnrolments', 'pendingClassConfirmations', 'pendingAssessments', 'upcomingAssessments', 'upcomingClasses', 'activeClasses', 'pendingResults', 'pendingLessons', 'upcomingLessons', 'stats'));
    }
}
