<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\BranchSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BranchSettingController extends Controller
{
    public function edit()
    {
        $branch = BranchSetting::current();
        $franchisorRegNumber = AppSetting::get('franchisor_registration_number');
        return view('admin.settings.branch', compact('branch', 'franchisorRegNumber'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'branch_name'                    => 'nullable|string|max:200',
            'address'                        => 'nullable|string',
            'email'                          => 'nullable|email|max:200',
            'phone'                          => 'nullable|string|max:50',
            'website'                        => 'nullable|string|max:200',
            'hero_image'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_hero_image'              => 'nullable|boolean',
            'enrolment_fee'                  => 'nullable|numeric|min:0',
            'private_lesson_fee'             => 'nullable|numeric|min:0',
            'bank_name'                      => 'nullable|string|max:200',
            'bank_account_name'              => 'nullable|string|max:200',
            'bank_account_number'            => 'nullable|string|max:100',
            'bank_branch_code'               => 'nullable|string|max:50',
            'bank_reference_note'            => 'nullable|string|max:200',
            'legal_entity_name'              => 'nullable|string|max:200',
            'legal_registration_number'      => 'nullable|string|max:100',
            'franchisor_registration_number' => 'nullable|string|max:100',
            'io_username'                    => 'nullable|string|max:200',
            'io_password'                    => 'nullable|string|max:200',
            'io_business_id'                 => 'nullable|string|max:100',
        ]);

        // Franchisor reg number is system-wide
        $franchisorReg = $data['franchisor_registration_number'] ?? null;
        unset($data['franchisor_registration_number']);
        AppSetting::updateOrCreate(
            ['key' => 'franchisor_registration_number'],
            ['value' => $franchisorReg, 'label' => 'McKaynine Training Registration Number']
        );

        // Hero image handling
        $branch = BranchSetting::current();
        unset($data['hero_image'], $data['remove_hero_image']);

        if ($request->boolean('remove_hero_image')) {
            if ($branch->hero_image_path) {
                Storage::disk('public')->delete($branch->hero_image_path);
            }
            $data['hero_image_path'] = null;
        } elseif ($request->hasFile('hero_image')) {
            if ($branch->hero_image_path) {
                Storage::disk('public')->delete($branch->hero_image_path);
            }
            $data['hero_image_path'] = $request->file('hero_image')->store('business/hero', 'public');
        }

        // Don't overwrite IO password if left blank (field shows masked placeholder)
        if (empty($data['io_password'])) {
            unset($data['io_password']);
        }

        $branch->update($data);
        return back()->with('success', 'Branch settings saved.');
    }
}
