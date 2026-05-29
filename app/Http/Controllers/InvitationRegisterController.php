<?php

namespace App\Http\Controllers;

use App\Models\Dog;
use App\Models\Handler;
use App\Models\Instructor;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InvitationRegisterController extends Controller
{
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (! $invitation->isValid()) {
            return view('invitation-invalid', [
                'reason' => $invitation->used_at ? 'already used' : 'expired',
            ]);
        }

        if ($invitation->isInstructor()) {
            return view('instructor-register', compact('invitation'));
        }

        return view('invitation-register', compact('invitation'));
    }

    // ── Handler (student) sign-up ─────────────────────────────────

    public function store(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        abort_unless($invitation->isValid(), 410, 'This invitation link is no longer valid.');
        abort_unless(! $invitation->isInstructor(), 400);

        $data = $request->validate([
            'first_name'            => ['required', 'string', 'max:100'],
            'last_name'             => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'cell_number'           => ['nullable', 'string', 'max:20'],
            'password'              => ['required', 'confirmed', 'min:8'],
            'dogs'                  => ['required', 'array', 'min:1'],
            'dogs.*.name'           => ['required', 'string', 'max:100'],
            'dogs.*.breed'          => ['nullable', 'string', 'max:100'],
            'dogs.*.date_of_birth'  => ['nullable', 'date', 'before:today'],
            'dogs.*.gender'         => ['nullable', 'in:male,female'],
            'dogs.*.photo'          => ['nullable', 'image', 'max:4096'],
        ], [
            'dogs.required'        => 'Please add at least one dog.',
            'dogs.*.name.required' => 'Each dog must have a name.',
        ]);

        $user = User::create([
            'name'       => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'is_handler' => true,
        ]);

        $handler = Handler::create([
            'user_id'     => $user->id,
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
            'cell_number' => $data['cell_number'] ?? null,
        ]);

        foreach ($data['dogs'] as $index => $dogData) {
            $photoPath = null;
            if (isset($request->file('dogs')[$index]['photo'])) {
                $photoPath = $request->file('dogs')[$index]['photo']->store('dogs/photos', 'public');
            }

            Dog::create([
                'handler_id'    => $handler->id,
                'name'          => $dogData['name'],
                'breed'         => $dogData['breed'] ?? null,
                'date_of_birth' => $dogData['date_of_birth'] ?? null,
                'gender'        => $dogData['gender'] ?? null,
                'photo_path'    => $photoPath,
            ]);
        }

        $invitation->markUsed();
        Auth::login($user);

        return redirect()->route('handler.dashboard')
            ->with('success', 'Welcome! Your account is set up. Your instructor will be in touch to confirm your class.');
    }

    // ── Instructor sign-up ────────────────────────────────────────

    public function storeInstructor(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        abort_unless($invitation->isValid(), 410, 'This invitation link is no longer valid.');
        abort_unless($invitation->isInstructor(), 400);

        // Pre-split name from invitation
        $nameParts  = explode(' ', $invitation->name ?? '', 2);
        $defaultFirst = $nameParts[0] ?? '';
        $defaultLast  = $nameParts[1] ?? '';

        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'password'    => ['required', 'confirmed', 'min:8'],
            'bio'         => ['nullable', 'string', 'max:1000'],
            'birthday'    => ['nullable', 'date', 'before:today'],
            'photo'       => ['nullable', 'image', 'max:4096'],
        ]);

        $user = User::create([
            'name'          => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'is_instructor' => true,
            'is_handler'    => false,
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('instructors/photos', 'public');
        }

        Instructor::create([
            'user_id'             => $user->id,
            'first_name'          => $data['first_name'],
            'last_name'           => $data['last_name'],
            'email'               => $data['email'],
            'phone'               => $data['phone'] ?? null,
            'bio'                 => $data['bio'] ?? null,
            'birthday'            => $data['birthday'] ?? null,
            'profile_photo_path'  => $photoPath,
            'is_active'           => true,
        ]);

        $invitation->markUsed();
        Auth::login($user);

        return redirect()->route('instructor.dashboard')
            ->with('success', 'Welcome! Your instructor account is ready.');
    }
}
