<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private function getInstructor(): Instructor
    {
        return Instructor::where('user_id', auth()->id())->firstOrFail();
    }

    public function edit()
    {
        $instructor = $this->getInstructor();
        return view('instructor.profile', compact('instructor'));
    }

    public function update(Request $request)
    {
        $instructor = $this->getInstructor();

        $data = $request->validate([
            'bio'      => 'nullable|string|max:2000',
            'phone'    => 'nullable|string|max:30',
            'birthday' => 'nullable|date|before:today',
            'photo'    => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($instructor->profile_photo_path) {
                Storage::disk('public')->delete($instructor->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('photo')
                ->store('instructor-photos', 'public');
        }

        unset($data['photo']);

        $instructor->update($data);

        return back()->with('success', 'Profile updated.');
    }
}
