<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\{Instructor, Invitation, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = Instructor::with(['user', 'classes'])->get();

        // Pending instructor invitations (not yet used, not expired)
        $pendingInvites = Invitation::where('type', 'instructor')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        return view('admin.instructors.index', compact('instructors', 'pendingInvites'));
    }

    /**
     * Send an invitation to a new instructor.
     */
    public function invite(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        // Prevent duplicate pending invites
        $existing = Invitation::where('email', $data['email'])
            ->where('type', 'instructor')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'A pending invitation already exists for this email.'])->withInput();
        }

        $invitation = Invitation::generate(
            email:     $data['email'],
            name:      trim($data['first_name'] . ' ' . $data['last_name']),
            createdBy: auth()->id(),
            type:      'instructor',
        );

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent to ' . $invitation->email);
    }

    /**
     * Resend an instructor invitation.
     */
    public function resendInvite(Invitation $invitation)
    {
        abort_unless($invitation->type === 'instructor', 403);
        $invitation->update(['expires_at' => now()->addDays(14)]);
        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation resent to ' . $invitation->email);
    }

    /**
     * Cancel / delete a pending instructor invitation.
     */
    public function cancelInvite(Invitation $invitation)
    {
        abort_unless($invitation->type === 'instructor', 403);
        $invitation->delete();
        return back()->with('success', 'Invitation cancelled.');
    }

    public function show(Instructor $instructor)
    {
        $instructor->load(['classes.dates', 'user']);
        return view('admin.instructors.show', compact('instructor'));
    }

    public function edit(Instructor $instructor)
    {
        return view('admin.instructors.edit', compact('instructor'));
    }

    public function update(Request $request, Instructor $instructor)
    {
        $request->validate([
            'payment_frequency' => 'nullable|in:termly,monthly',
        ]);

        $instructor->update(array_merge(
            $request->only(['first_name', 'last_name', 'phone', 'bio', 'is_active']),
            ['payment_frequency' => $request->input('payment_frequency', 'termly')]
        ));

        return redirect()->route('admin.instructors.show', $instructor)->with('success', 'Instructor updated.');
    }
}
