<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;

class AchievementController extends Controller
{
    public function index()
    {
        $handler = auth()->user()->handler;

        // Mark achievement notifications as read
        auth()->user()->appNotifications()
            ->where('type', 'achievement')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $results = $handler->enrolments()
            ->whereHas('examResult', fn($q) => $q->where('status', 'released'))
            ->with(['dog', 'dogClass.classType', 'examResult'])
            ->get();

        return view('handler.achievements.index', compact('results'));
    }
}
