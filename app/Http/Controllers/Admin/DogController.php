<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Dog, DogClass};
use Illuminate\Http\Request;

class DogController extends Controller
{
    public function show(Dog $dog)
    {
        $dog->load([
            'handler',
            'enrolments.dogClass.classType',
            'enrolments.examResult',
            'enrolments.goals',
            'assessmentRequests.slot',
            'assessmentRequests.scores',
        ]);

        $enrolledClassIds = $dog->enrolments
            ->whereNotIn('status', ['withdrawn'])
            ->pluck('class_id')
            ->filter()
            ->all();

        $availableClasses = DogClass::whereNotNull('start_date')
            ->where('end_date', '>=', now())
            ->whereNotIn('id', $enrolledClassIds)
            ->with('classType')
            ->orderBy('start_date')
            ->get();

        return view('admin.dogs.show', compact('dog', 'availableClasses'));
    }

    public function toggleMultiDogDiscount(Dog $dog)
    {
        $dog->update(['multi_dog_discount' => !$dog->multi_dog_discount]);
        $status = $dog->multi_dog_discount ? 'enabled' : 'removed';
        return back()->with('success', "Multi-dog discount {$status} for {$dog->name}.");
    }
}
