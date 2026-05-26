<?php

namespace App\Http\Controllers\Handler;

use App\Http\Controllers\Controller;
use App\Models\Enrolment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EnrolmentFormController extends Controller
{
    public function uploadDogPhoto(Request $request)
    {
        $request->validate(['file' => 'required|file|image|max:10240']);
        $path = $request->file('file')->store('dogs/photos', 'public');
        return response()->json(['path' => $path]);
    }

    public function show()
    {
        $user    = auth()->user();
        $handler = $user->handler;

        abort_if(!$handler, 403, 'No handler profile found.');

        $assessment = $handler->assessmentRequests()
            ->where('status', 'completed')
            ->with('scores', 'dog')
            ->latest()
            ->first();

        abort_if(!$assessment, 403, 'No completed assessment found.');

        $dog     = $assessment->dog;
        $outcome = $assessment->scores?->final_outcome ?? $assessment->scores?->recommended_outcome;

        return view('enrolment.enrol2', compact('handler', 'dog', 'assessment', 'outcome'));
    }

    public function store(Request $request)
    {
        $user    = auth()->user();
        $handler = $user->handler;
        $dog     = $handler->assessmentRequests()
            ->where('status', 'completed')
            ->latest()
            ->first()
            ?->dog;

        abort_if(!$handler || !$dog, 403);

        $data = $request->validate([
            'cell_number'           => 'required|string|max:20',
            'occupation'            => 'nullable|string|max:100',
            'vet_name_location'     => 'required|string|max:200',
            'account_holder_name'   => 'nullable|string|max:100',
            'hear_about_us'         => 'nullable|string|max:200',
            'microchip_number'      => 'nullable|string|max:50',
            'vaccination_expiry'    => 'nullable|date',
            'vaccination_card_path' => 'nullable|string|max:500',
            'dog_photo_path'        => 'nullable|string|max:500',
            'whatsapp_permission'   => 'nullable|boolean',
            'social_media_permission' => 'nullable|boolean',
            'ground_rules_agreed'   => 'accepted',
            'terms_agreed'          => 'accepted',
        ]);

        $handler->update([
            'cell_number'             => $data['cell_number'],
            'occupation'              => $data['occupation'] ?? $handler->occupation,
            'vet_name_location'       => $data['vet_name_location'],
            'account_holder_name'     => $data['account_holder_name'] ?? $handler->account_holder_name,
            'hear_about_us'           => $data['hear_about_us'] ?? $handler->hear_about_us,
            'whatsapp_permission'     => $request->boolean('whatsapp_permission'),
            'social_media_permission' => $request->boolean('social_media_permission'),
            'ground_rules_agreed'     => true,
            'terms_agreed'            => true,
        ]);

        $dogUpdate = ['microchip_number' => $data['microchip_number'] ?? $dog->microchip_number];
        if (!empty($data['vaccination_expiry'])) {
            $dogUpdate['vaccination_expiry_date'] = $data['vaccination_expiry'];
        }
        if (!empty($data['vaccination_card_path'])) {
            $dogUpdate['vaccination_card_path'] = $data['vaccination_card_path'];
        }
        if (!empty($data['dog_photo_path'])) {
            $dogUpdate['photo_path'] = $data['dog_photo_path'];
        }
        $dog->update($dogUpdate);

        Enrolment::firstOrCreate(
            ['handler_id' => $handler->id, 'dog_id' => $dog->id, 'status' => 'pending'],
            ['enrolled_at' => now(), 'pathway' => 'assessment']
        );

        return redirect()->route('handler.dashboard')->with('success', 'Enrolment submitted! Our team will be in touch to confirm your class placement.');
    }
}
