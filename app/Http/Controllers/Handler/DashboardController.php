<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\Resource;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $handler = $user->handler;

        if (!$handler) {
            return redirect()->route('enrol.start');
        }

        $enrolments = $handler->enrolments()
            ->whereIn('status', ['confirmed', 'pending'])
            ->with(['dogClass', 'dog'])
            ->get();

        $dogs = $handler->dogs()->with('enrolments.dogClass')->get();

        $recentResources = Resource::where('is_active', true)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $pendingEnrolment = $handler->assessmentRequests()
            ->where('status', 'completed')
            ->exists() && $enrolments->isEmpty();

        // Recommended classes for pending assessment enrolments
        $recommendedClassTypes = collect();
        $pendingAssessmentEnrolment = $handler->enrolments()
            ->where('pathway', 'assessment')
            ->whereIn('status', ['pending', 'pending_class_assignment'])
            ->with('assessmentRequest.scores')
            ->first();

        if ($pendingAssessmentEnrolment) {
            $ids = $pendingAssessmentEnrolment->assessmentRequest?->scores?->recommended_class_ids ?? [];
            if (!empty($ids)) {
                $recommendedClassTypes = \App\Models\ClassType::whereIn('id', $ids)
                    ->where('info_page_enabled', true)
                    ->get();
            }
        }

        return view('handler.dashboard', compact('handler', 'enrolments', 'dogs', 'recentResources', 'pendingEnrolment', 'recommendedClassTypes'));
    }
}
