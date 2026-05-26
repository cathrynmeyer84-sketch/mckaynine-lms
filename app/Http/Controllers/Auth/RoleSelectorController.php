<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoleSelectorController extends Controller
{
    /**
     * Show the role picker, or skip it if the user only has one role.
     */
    public function show(Request $request)
    {
        $roles = $this->rolesFor($request->user());

        if (count($roles) === 1) {
            return $this->redirectForRole($roles[0]);
        }

        return view('auth.select-role', compact('roles'));
    }

    /**
     * Process the chosen role and redirect to the matching dashboard.
     */
    public function select(Request $request): RedirectResponse
    {
        $request->validate(['role' => 'required|in:admin,instructor,handler']);

        $user = $request->user();
        $role = $request->role;

        // Guard against picking a role the user doesn't actually have
        if ($role === 'admin'      && ! $user->is_admin)      abort(403);
        if ($role === 'instructor' && ! $user->is_instructor) abort(403);
        if ($role === 'handler'    && ! $user->is_handler)    abort(403);

        return $this->redirectForRole($role);
    }

    // ─────────────────────────────────────────────

    private function rolesFor($user): array
    {
        $roles = [];
        if ($user->is_admin)      $roles[] = 'admin';
        if ($user->is_instructor) $roles[] = 'instructor';
        if ($user->is_handler)    $roles[] = 'handler';

        return $roles ?: ['handler']; // fallback
    }

    private function redirectForRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'instructor' => redirect()->route('instructor.dashboard'),
            default      => redirect()->route('handler.dashboard'),
        };
    }
}
