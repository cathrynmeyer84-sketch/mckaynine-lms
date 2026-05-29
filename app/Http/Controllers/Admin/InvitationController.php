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

    public function storeCsv(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file   = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $sent      = [];
        $skipped   = [];
        $row       = 0;
        $hasHeader = null;

        while (($line = fgetcsv($handle)) !== false) {
            $row++;

            // Skip completely empty rows
            $line = array_map('trim', $line);
            if (empty(array_filter($line))) continue;

            // Auto-detect header row on first non-empty line
            if ($hasHeader === null) {
                $firstCell = strtolower($line[0] ?? '');
                $hasHeader = in_array($firstCell, ['email', 'e-mail', 'email address']);
                if ($hasHeader) continue;
            }

            $email = strtolower($line[0] ?? '');
            $name  = $line[1] ?? null;

            // Validate email
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped[] = ['value' => $email ?: "(row {$row})", 'reason' => 'invalid email'];
                continue;
            }

            // Skip duplicates (pending invite already exists)
            $existing = Invitation::where('email', $email)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->first();

            if ($existing) {
                $skipped[] = ['value' => $email, 'reason' => 'already has a pending invite'];
                continue;
            }

            // Skip if they already have an account
            if (\App\Models\User::where('email', $email)->exists()) {
                $skipped[] = ['value' => $email, 'reason' => 'account already exists'];
                continue;
            }

            $invitation = Invitation::generate(
                email:     $email,
                name:      $name ?: null,
                createdBy: auth()->id(),
            );

            Mail::to($invitation->email)->send(new InvitationMail($invitation));
            $sent[] = $email;
        }

        fclose($handle);

        $summary = count($sent) . ' invitation' . (count($sent) !== 1 ? 's' : '') . ' sent.';
        if (count($skipped)) {
            $summary .= ' ' . count($skipped) . ' skipped (see below).';
        }

        return back()
            ->with('csv_sent', $sent)
            ->with('csv_skipped', $skipped)
            ->with('success', $summary);
    }

    public function sampleCsv()
    {
        $csv = "email,name\njane@example.com,Jane Smith\njohn@example.com,John\nsarah@example.com,\n";
        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invitations-sample.csv"',
        ]);
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
