<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ClassDate;
use App\Models\Enrolment;
use App\Models\PrivateLesson;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $instructor = $user->instructor;

        $today = now()->startOfDay();
        $classes = $instructor->classes()
            ->where('end_date', '>=', $today)
            ->whereNotNull('start_date')
            ->with(['enrolments' => fn($q) => $q->where('status', 'confirmed'), 'classDates'])
            ->get();

        $classIds = $classes->pluck('id');

        $upcomingDates = ClassDate::whereIn('class_id', $classIds)
            ->where('is_off_week', false)
            ->whereDate('date', '>=', today())
            ->with('dogClass')
            ->orderBy('date')
            ->take(5)
            ->get();

        $pendingRegisters = ClassDate::whereIn('class_id', $classIds)
            ->where('is_off_week', false)
            ->whereDate('date', '<', today())
            ->doesntHave('registers')
            ->with('dogClass')
            ->orderBy('date')
            ->get();

        $pendingGrades = Enrolment::whereIn('class_id', $classes->pluck('id'))
            ->where('status', 'confirmed')
            ->whereDoesntHave('examResult')
            ->whereHas('dogClass', fn($q) => $q->where('status', 'completed'))
            ->count();

        $pendingLessons = $instructor ? PrivateLesson::where('instructor_id', $instructor->id)
            ->where('status', 'pending')
            ->with(['handler', 'dog'])
            ->latest()
            ->get() : collect();

        $upcomingLessons = $instructor ? PrivateLesson::where('instructor_id', $instructor->id)
            ->where('status', 'confirmed')
            ->whereDate('requested_date', '>=', Carbon::today())
            ->with(['handler', 'dog'])
            ->orderBy('requested_date')
            ->orderBy('requested_start_time')
            ->take(5)
            ->get() : collect();

        return view('instructor.dashboard', compact(
            'instructor', 'classes', 'upcomingDates', 'pendingRegisters', 'pendingGrades',
            'pendingLessons', 'upcomingLessons'
        ));
    }
}
