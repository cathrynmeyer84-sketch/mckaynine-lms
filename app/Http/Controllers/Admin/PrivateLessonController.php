<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivateLesson;

class PrivateLessonController extends Controller
{
    public function index()
    {
        $lessons = PrivateLesson::with(['handler', 'dog', 'instructor'])
            ->latest()->paginate(30);
        return view('admin.private-lessons.index', compact('lessons'));
    }

    public function show(PrivateLesson $lesson)
    {
        $lesson->load(['handler', 'dog', 'instructor']);
        return view('admin.private-lessons.show', compact('lesson'));
    }
}
