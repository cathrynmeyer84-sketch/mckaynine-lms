<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with('creator')
            ->latest()
            ->paginate(30);

        return view('admin.invitations.index', compact('invitations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name'  => ['nullable', 'string', 'max:100'],
        ]);

        // Prevent duplicate pending invites to the same email
        $existing = Invitation::where('email', $data['email'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'A pending invitation already exists for this email address.'])->withInput();
        }

        $invitation = Invitation::generate(
            email: $data['email'],
            name:  $data['name'] ?? null,
            createdBy: auth()->id(),
        );

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent to ' . $invitation->email);
    }

    public function destroy(Invitation $invitation)
    {
        $invitation->delete();
        return back()->with('success', 'Invitation deleted.');
    }

    public function resend(Invitation $invitation)
    {
        // Extend expiry and resend
        $invitation->update(['expires_at' => now()->addDays(14)]);
        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation resent to ' . $invitation->email);
    }
}
