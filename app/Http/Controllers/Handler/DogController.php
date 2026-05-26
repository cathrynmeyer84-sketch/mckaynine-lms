<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DogController extends Controller
{
    public function index()
    {
        $handler = auth()->user()->handler;
        $dogs = $handler->dogs()->with('enrolments.dogClass')->get();
        return view('handler.dogs.index', compact('dogs'));
    }

    public function edit(Dog $dog)
    {
        $handler = auth()->user()->handler;
        abort_unless($dog->handler_id === $handler->id, 403);
        return view('handler.dogs.edit', compact('dog'));
    }

    public function update(Request $request, Dog $dog)
    {
        $handler = auth()->user()->handler;
        abort_unless($dog->handler_id === $handler->id, 403);

        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'breed'                 => 'nullable|string|max:100',
            'date_of_birth'         => 'nullable|date',
            'microchip_number'      => 'nullable|string|max:50',
            'vaccination_expiry'    => 'nullable|date',
            'photo_path'            => 'nullable|string',
            'vaccination_card_path' => 'nullable|string',
        ]);

        if ($request->filled('photo_path') && $request->input('photo_path') !== $dog->photo_path) {
            if ($dog->photo_path) Storage::disk('public')->delete($dog->photo_path);
        }

        if ($request->filled('vaccination_card_path') && $request->input('vaccination_card_path') !== $dog->vaccination_card_path) {
            if ($dog->vaccination_card_path) Storage::disk('public')->delete($dog->vaccination_card_path);
        }

        $dog->update($data);

        return redirect()->route('handler.dogs.index')->with('success', 'Dog profile updated.');
    }
}
