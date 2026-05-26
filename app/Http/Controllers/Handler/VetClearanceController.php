<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\Enrolment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VetClearanceController extends Controller
{
    public function show(Enrolment $enrolment)
    {
        $this->authorizeEnrolment($enrolment);
        return view('handler.vet-clearance', compact('enrolment'));
    }

    public function upload(Request $request, Enrolment $enrolment)
    {
        $this->authorizeEnrolment($enrolment);

        $request->validate([
            'vet_clearance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('vet_clearance')->store('vet-clearance', 'public');

        $enrolment->update([
            'vet_clearance_path' => $path,
            'status'             => 'vet_clearance_review',
        ]);

        return back()->with('success', 'Certificate uploaded. We\'ll review it and be in touch shortly.');
    }

    private function authorizeEnrolment(Enrolment $enrolment): void
    {
        $handler = Auth::user()->handler;
        abort_unless($handler && $enrolment->handler_id === $handler->id, 403);
    }
}
