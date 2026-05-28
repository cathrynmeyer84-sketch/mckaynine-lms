<?php

namespace App\Http\Controllers;

use App\Mail\AccountLinkRequest;
use App\Mail\AssessmentConfirmed;
use App\Mail\NewAssessmentNotification;
use App\Mail\NewEnrolmentNotification;
use App\Models\AppNotification;
use App\Models\AppSetting;
use App\Models\AssessmentAvailability;
use App\Models\AssessmentRequest;
use App\Models\AssessmentSlot;
use App\Models\AssessmentSpecialDate;
use App\Models\CalendarDay;
use App\Models\CalendarWeek;
use App\Models\Dog;
use App\Models\DogClass;
use App\Models\Enrolment;
use App\Models\Handler;
use App\Models\User;
use App\Services\MessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class EnrolmentController extends Controller
{
    // -------------------------------------------------------------------------
    // Landing — DOB check
    // -------------------------------------------------------------------------

    public function start(Request $request)
    {
        $classId = $request->query('class_id');
        return view('enrolment.start', compact('classId'));
    }

    // -------------------------------------------------------------------------
    // Dog Picker — logged-in handlers choose which dog to enrol
    // -------------------------------------------------------------------------

    public function chooseDog(Request $request)
    {
        $classId   = $request->query('class_id');
        $handler   = auth()->user()->handler;

        if (!$handler) {
            return redirect()->route('enrol.assessment', $classId ? ['class_id' => $classId] : []);
        }

        $classType = $classId ? \App\Models\DogClass::find($classId)?->classType : null;

        $dogs = $handler->dogs()->where('is_retired', false)->get()->map(function ($dog) use ($classType) {
            $dog->eligible          = $classType ? $dog->eligibleFor($classType) : true;
            $dog->missingPrereqs    = $classType ? $dog->missingPrerequisitesFor($classType) : collect();
            return $dog;
        });

        return view('enrolment.choose-dog', compact('dogs', 'classType', 'classId', 'handler'));
    }

    // -------------------------------------------------------------------------
    // Existing handler enrolling an existing dog
    // -------------------------------------------------------------------------

    public function storeExistingDog(Request $request)
    {
        $request->validate([
            'dog_id'   => 'required|exists:dogs,id',
            'class_id' => 'required|exists:classes,id',
            'pathway'  => 'nullable|in:existing,assessment',
        ]);

        $handler  = auth()->user()->handler;
        $dog      = Dog::where('id', $request->dog_id)->where('handler_id', $handler->id)->firstOrFail();
        $dogClass = DogClass::findOrFail($request->class_id);

        // Prevent duplicate enrolments in the same class
        $existing = Enrolment::where('handler_id', $handler->id)
            ->where('dog_id', $dog->id)
            ->where('class_id', $dogClass->id)
            ->whereNotIn('status', ['withdrawn', 'rejected'])
            ->first();

        if ($existing) {
            return redirect()->route('handler.classes.show', $existing)
                ->with('info', "{$dog->name} is already enrolled in a class.");
        }

        Enrolment::create([
            'handler_id'  => $handler->id,
            'dog_id'      => $dog->id,
            'class_id'    => $dogClass->id,
            'status'      => 'pending',
            'pathway'     => $request->input('pathway', 'existing'),
            'branch'      => 'honeydew',
            'enrolled_at' => now(),
        ]);

        // Notify admin by email
        $adminEmail = AppSetting::get('admin_email');
        if ($adminEmail) {
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                new \App\Mail\NewEnrolmentNotification($handler, $dog, $dogClass)
            );
        }

        return redirect()->route('enrol.existing-dog.confirmed', [
            'dog'   => $dog->name,
            'class' => $dogClass->name,
        ]);
    }

    public function existingDogConfirmed(Request $request)
    {
        abort_unless($request->has('dog') && $request->has('class'), 404);
        return view('enrolment.existing-confirmed', [
            'dogName'   => $request->query('dog'),
            'className' => $request->query('class'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Puppy Class (< 4 months)
    // -------------------------------------------------------------------------

    public function puppyForm(Request $request)
    {
        $dob = $request->query('dob', '');
        $selectedClassId = $request->query('class_id');

        $today = now()->startOfDay();

        // Only show classes belonging to puppy-type class types
        $puppyQuery = fn($q) => $q->whereHas('classType', fn($ct) => $ct->where('is_entry_class', true));

        // Upcoming puppy classes
        $upcoming = DogClass::where('start_date', '>', $today)
            ->whereHas('classType', fn($ct) => $ct->where('is_entry_class', true))
            ->orderBy('start_date')
            ->get();

        // Active puppy classes where the 2nd scheduled date hasn't passed yet
        $activeEnrollable = DogClass::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereHas('classType', fn($ct) => $ct->where('is_entry_class', true))
            ->with(['scheduledDates' => fn($q) => $q->where('is_off_week', false)->orderBy('date')])
            ->get()
            ->filter(function ($class) use ($today) {
                $dates = $class->scheduledDates;
                return $dates->count() >= 2 && $dates->get(1)->date->gte($today);
            });

        $classes = $activeEnrollable->merge($upcoming)->sortBy('start_date')->values();

        return view('enrolment.form', compact('dob', 'classes', 'selectedClassId'));
    }

    public function uploadVaccination(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf|max:10240',
        ]);

        $path = $request->file('file')->store('dogs/vaccinations', 'public');

        return response()->json(['path' => $path]);
    }

    public function storePuppy(Request $request)
    {
        $data = $request->validate([
            // Owner
            'email'                        => 'required|email|max:200',
            'first_name'                   => 'required|string|max:100',
            'last_name'                    => 'required|string|max:100',
            'cell_number'                  => 'required|string|max:20',
            'occupation'                   => 'nullable|string|max:100',
            'vet_name_location'            => 'required|string|max:200',
            'is_account_holder'            => 'required|in:yes,no',
            'account_holder_first_name'    => 'required_if:is_account_holder,no|nullable|string|max:100',
            'account_holder_last_name'     => 'required_if:is_account_holder,no|nullable|string|max:100',
            'account_holder_email'         => 'required_if:is_account_holder,no|nullable|email|max:200',
            'whatsapp_consent'             => 'required|in:yes,no,unsure',
            'photo_consent'                => 'required|in:yes,no,unsure',
            'hear_about_us_sources'        => 'nullable|array',
            'hear_about_us_sources.*'      => 'string|max:50',
            'hear_about_us_other'          => 'nullable|string|max:200',
            'ground_rules_agreed'          => 'accepted',
            'terms_agreed'                 => 'accepted',
            // Dog
            'dog_name'                     => 'required|string|max:100',
            'dog_dob'                      => 'required|date|before:today',
            'gender'                       => 'required|in:male,female',
            'dog_breed'                    => 'required|string|max:100',
            'spay_neuter_status'           => 'required|in:when_old_enough,already_done,not_planning',
            'origin_story'                 => 'required|string|max:100',
            'age_when_acquired'            => 'required|string|max:100',
            'animal_buddies_at_home'       => 'required|array|min:1',
            'animal_buddies_at_home.*'     => 'string|max:50',
            'young_children_at_home'       => 'required|array|min:1',
            'young_children_at_home.*'     => 'string|max:50',
            'socialisation_other_dogs'     => 'required|in:great,ok,not_good',
            'socialisation_other_animals'  => 'required|in:great,ok,not_good',
            'socialisation_people'         => 'required|in:great,ok,not_good',
            'training_goal'                => 'required|in:competitive_dog_sport,chilled_canine_companion',
            'has_behaviour_problems'       => 'required|boolean',
            'behaviour_problems_details'   => 'nullable|string',
            'has_health_issues'            => 'required|boolean',
            'health_issues'                => 'nullable|string',
            'vaccination_card'             => 'nullable|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/heic,image/heif,application/pdf|max:10240',
            'vaccination_card_path'        => 'nullable|string|max:500',
            // Checklist
            'onlead_socialising'           => 'accepted',
            'equipment_supervision'        => 'accepted',
            'training_equipment'           => 'accepted',
            'treats'                       => 'accepted',
            'waste_bags'                   => 'accepted',
            'class_id'                     => 'nullable|exists:classes,id',
        ]);

        $newUser = null;
        $newHandler = null;
        DB::transaction(function () use ($request, $data, &$newUser, &$newHandler) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['first_name'] . ' ' . $data['last_name'],
                    'password'          => bcrypt(Str::random(32)),
                    'is_handler'        => true,
                    'is_active'         => false,
                    'email_verified_at' => now(),
                ]
            );
            if ($user->wasRecentlyCreated) $newUser = $user;

            $handler = $user->handler ?? Handler::create([
                'user_id'               => $user->id,
                'first_name'            => $data['first_name'],
                'last_name'             => $data['last_name'],
                'cell_number'           => $data['cell_number'],
                'occupation'            => $data['occupation'] ?? null,
                'vet_name_location'     => $data['vet_name_location'],
                'whatsapp_consent'      => $data['whatsapp_consent'],
                'photo_consent'         => $data['photo_consent'],
                'hear_about_us_sources' => array_filter(array_merge(
                    array_diff($data['hear_about_us_sources'] ?? [], ['other']),
                    !empty($data['hear_about_us_other']) ? ['other: ' . $data['hear_about_us_other']] : []
                )),
                'ground_rules_agreed'   => true,
                'terms_agreed'          => true,
                'status'                => 'pending',
            ]);
            $newHandler = $handler;

            // Create separate account holder record if handler is not the account holder
            if ($data['is_account_holder'] === 'no' && !$handler->accountHolder()->exists()) {
                $ahName  = trim($data['account_holder_first_name'] . ' ' . $data['account_holder_last_name']);
                $ahEmail = $data['account_holder_email'];

                // Check if this email belongs to an existing McKaynine handler
                $linkedUser    = User::where('email', $ahEmail)->whereHas('handler')->first();
                $linkedHandler = $linkedUser?->handler;

                if ($linkedHandler && $linkedHandler->id !== $handler->id) {
                    // It's a McKaynine member — create a pending link, send approval email
                    $token = Str::random(48);
                    $accountHolder = $handler->accountHolder()->create([
                        'name'              => $ahName,
                        'email'             => $ahEmail,
                        'linked_handler_id' => $linkedHandler->id,
                        'link_status'       => 'pending_approval',
                        'link_token'        => $token,
                        'link_expires_at'   => now()->addDays(7),
                    ]);
                    Mail::to($ahEmail)->send(new AccountLinkRequest($handler, $accountHolder));
                } else {
                    // External contact — store as-is
                    $handler->accountHolder()->create([
                        'name'  => $ahName,
                        'email' => $ahEmail,
                    ]);
                }
            }

            if (!$user->is_handler) {
                $user->update(['is_handler' => true]);
            }

            $vaccinationPath = $data['vaccination_card_path']
                ?? ($request->hasFile('vaccination_card')
                    ? $request->file('vaccination_card')->store('dogs/vaccinations', 'public')
                    : null);

            abort_if(!$vaccinationPath, 422, 'Vaccination card is required.');

            $dog = Dog::create([
                'handler_id'                  => $handler->id,
                'name'                        => $data['dog_name'],
                'breed'                       => $data['dog_breed'] ?? null,
                'date_of_birth'               => $data['dog_dob'],
                'gender'                      => $data['gender'] ?? null,
                'spay_neuter_status'          => $data['spay_neuter_status'] ?? null,
                'origin_story'                => $data['origin_story'] ?? null,
                'age_when_acquired'           => $data['age_when_acquired'] ?? null,
                'animal_buddies_at_home'      => $data['animal_buddies_at_home'] ?? [],
                'young_children_at_home'      => $data['young_children_at_home'] ?? [],
                'socialisation_other_dogs'    => $data['socialisation_other_dogs'] ?? null,
                'socialisation_other_animals' => $data['socialisation_other_animals'] ?? null,
                'socialisation_people'        => $data['socialisation_people'] ?? null,
                'training_goal'               => $data['training_goal'] ?? null,
                'has_behaviour_problems'      => isset($data['has_behaviour_problems']) ? (bool) $data['has_behaviour_problems'] : null,
                'behaviour_problems_details'  => $data['behaviour_problems_details'] ?? null,
                'has_health_issues'           => isset($data['has_health_issues']) ? (bool) $data['has_health_issues'] : null,
                'health_issues'               => $data['health_issues'] ?? null,
                'vaccination_card_path'       => $vaccinationPath,
                'vaccination_expiry_date'     => $data['vaccination_expiry'] ?? null,
            ]);

            Enrolment::create([
                'handler_id'             => $handler->id,
                'dog_id'                 => $dog->id,
                'class_id'               => $data['class_id'] ?? null,
                'status'                 => 'pending',
                'pathway'                => 'puppy',
                'class_type_requested'   => 'puppy',
                'branch'                 => 'honeydew',
                'checklist_acknowledged' => true,
                'enrolled_at'            => now(),
            ]);
        });

        if ($newUser) {
            try {
                app(MessageService::class)->sendTemplateToHandler(
                    'app_welcome',
                    $newUser,
                    ['handler' => $newHandler, 'dog' => null, 'class' => null],
                    null,
                    null
                );
            } catch (\Throwable) {}
        }

        return redirect()->route('enrol.submitted')->with('success', 'Enrolment submitted! We\'ll be in touch shortly.');
    }

    // -------------------------------------------------------------------------
    // Assessment (≥ 4 months)
    // -------------------------------------------------------------------------

    public function assessmentForm(Request $request)
    {
        $dob = $request->query('dob', '');
        return view('enrolment.assessment', compact('dob'));
    }

    public function storeAssessment(Request $request)
    {
        $data = $request->validate([
            // Owner
            'email'                        => 'required|email|max:200',
            'first_name'                   => 'required|string|max:100',
            'last_name'                    => 'required|string|max:100',
            'cell_number'                  => 'required|string|max:20',
            'terms_agreed'                 => 'accepted',
            // Dog
            'dog_name'                     => 'required|string|max:100',
            'dog_breed'                    => 'nullable|string|max:100',
            'dog_dob'                      => 'nullable|date|before:today',
            'dog_age_description'          => 'required|string|max:200',
            'gender_repro_status'          => 'required|string|max:50',
            'where_got_dog'                => 'required|string|max:100',
            'age_acquired'                 => 'required|string|max:50',
            'how_long_had_dog'             => 'required|string|max:200',
            'health_concerns_yes'          => 'required|in:yes,no',
            'health_concerns'              => 'nullable|string',
            'prior_training_yes'           => 'required|in:yes,no',
            'vaccination_card_path'        => 'nullable|string|max:500',
            // Goals
            'training_goals'               => 'nullable|array',
            'training_goals.*'             => 'string|max:100',
            'training_goals_other_detail'  => 'nullable|string|max:500',
            'desired_outcomes'             => 'nullable|string',
            'specific_issues'              => 'nullable|string',
            // Behaviour
            'response_to_new_people'       => 'nullable|integer|min:1|max:5',
            'behaviour_around_dogs'        => 'nullable|string',
            'aggression_targets'           => 'nullable|array',
            'aggression_targets.*'         => 'string|max:100',
            'aggression_details'           => 'nullable|string',
            'prior_training'               => 'nullable|string',
            'comfort_in_busy_environments' => 'nullable|string',
            // Assessment
            'comfortable_with_assessment'  => 'nullable|string',
            'open_to_recommendation'       => 'required|string|max:100',
            // Final
            'additional_notes'             => 'nullable|string',
            // Checklist
            'checklist_collar'             => 'accepted',
            'checklist_treats'             => 'accepted',
            'checklist_follow_staff'       => 'accepted',
            'checklist_no_onlead'          => 'accepted',
            'checklist_clean_up'           => 'accepted',
        ]);

        $ar = null;
        $newAssessmentUser = null;
        $newAssessmentHandler = null;
        DB::transaction(function () use ($request, $data, &$ar, &$newAssessmentUser, &$newAssessmentHandler) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['first_name'] . ' ' . $data['last_name'],
                    'password'          => bcrypt(Str::random(32)),
                    'is_handler'        => true,
                    'is_active'         => false,
                    'email_verified_at' => now(),
                ]
            );
            if ($user->wasRecentlyCreated) $newAssessmentUser = $user;

            $handler = $user->handler ?? Handler::create([
                'user_id'             => $user->id,
                'first_name'          => $data['first_name'],
                'last_name'           => $data['last_name'],
                'cell_number'         => $data['cell_number'],
                'ground_rules_agreed' => true,
                'terms_agreed'        => true,
                'status'              => 'pending',
            ]);
            $newAssessmentHandler = $handler;

            if (!$user->is_handler) {
                $user->update(['is_handler' => true]);
            }

            $dogData = [
                'handler_id'    => $handler->id,
                'name'          => $data['dog_name'],
                'breed'         => $data['dog_breed'] ?? null,
                'date_of_birth' => $data['dog_dob'] ?? null,
            ];

            if (!empty($data['vaccination_card_path'])) {
                $dogData['vaccination_card_path'] = $data['vaccination_card_path'];
            }

            $dog = Dog::create($dogData);

            $aggressionTargets = $data['aggression_targets'] ?? [];
            $aggressionHistory = !in_array('No — never shown aggression', $aggressionTargets) && count($aggressionTargets) > 0
                ? implode(', ', $aggressionTargets)
                : null;

            $ar = AssessmentRequest::create([
                'handler_id'                   => $handler->id,
                'dog_id'                       => $dog->id,
                'dog_age_description'          => $data['dog_age_description'] ?? null,
                'gender_repro_status'          => $data['gender_repro_status'] ?? null,
                'where_got_dog'                => $data['where_got_dog'] ?? null,
                'age_acquired'                 => $data['age_acquired'] ?? null,
                'how_long_had_dog'             => $data['how_long_had_dog'] ?? null,
                'health_concerns'              => ($data['health_concerns_yes'] === 'yes') ? ($data['health_concerns'] ?? null) : null,
                'training_goals'               => $data['training_goals'] ?? [],
                'desired_outcomes'             => $data['desired_outcomes'] ?? null,
                'specific_issues'              => $data['specific_issues'] ?? null,
                'response_to_new_people'       => $data['response_to_new_people'] ?? null,
                'behaviour_around_dogs'        => $data['behaviour_around_dogs'] ?? null,
                'aggression_history'           => $aggressionHistory,
                'aggression_targets'           => $aggressionTargets,
                'aggression_details'           => $data['aggression_details'] ?? null,
                'prior_training'               => ($data['prior_training_yes'] === 'yes') ? ($data['prior_training'] ?? null) : null,
                'comfort_in_busy_environments' => $data['comfort_in_busy_environments'] ?? null,
                'comfortable_with_assessment'  => $data['comfortable_with_assessment'] ?? null,
                'open_to_recommendation'       => $data['open_to_recommendation'],
                'additional_notes'             => $data['additional_notes'] ?? null,
                'terms_agreed'                 => true,
                'requirements_acknowledged'    => true,
                'status'                       => 'pending',
            ]);
        });

        if ($newAssessmentUser) {
            try {
                app(MessageService::class)->sendTemplateToHandler(
                    'app_welcome',
                    $newAssessmentUser,
                    ['handler' => $newAssessmentHandler, 'dog' => null, 'class' => null],
                    null,
                    null
                );
            } catch (\Throwable) {}
        }

        // Notify admins
        $adminEmail = AppSetting::get('admin_email');
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new NewAssessmentNotification($ar));
        }

        // In-app notification for all admin users
        User::where('is_admin', true)->each(function (User $admin) use ($ar) {
            AppNotification::create([
                'user_id' => $admin->id,
                'type'    => 'new_assessment',
                'title'   => 'New Assessment Request',
                'message' => ($ar->handler?->first_name . ' ' . $ar->handler?->last_name) . ' submitted an assessment form for ' . $ar->dog?->name . '.',
                'data'    => ['assessment_request_id' => $ar->id],
            ]);
        });

        return redirect()->route('enrol.submitted')->with('success', 'Assessment request submitted! We\'ll be in touch to schedule a time.');
    }

    // -------------------------------------------------------------------------
    // Shared
    // -------------------------------------------------------------------------

    public function submitted()
    {
        return view('enrolment.submitted');
    }

    public function selectSlot(AssessmentRequest $assessmentRequest)
    {
        $handler = auth()->user()->handler;
        abort_unless($assessmentRequest->handler_id === $handler->id, 403);

        $slots = $this->generateAvailableSlots(42);

        return view('enrolment.select-slot', compact('assessmentRequest', 'slots'));
    }

    public function bookSlot(Request $request, AssessmentRequest $assessmentRequest)
    {
        $handler = auth()->user()->handler;
        abort_unless($assessmentRequest->handler_id === $handler->id, 403);

        $data = $request->validate(['slot_key' => 'required|string']);
        [$date, $startTime] = explode('|', $data['slot_key']);

        [$slot, $error] = $this->findOrCreateSlot($date, $startTime);
        abort_if($error, 422, $error ?? 'Slot not available.');

        $assessmentRequest->update(['assessment_slot_id' => $slot->id, 'status' => 'booked']);
        $assessmentRequest->load(['handler.user', 'dog', 'slot']);
        if ($email = $assessmentRequest->handler?->user?->email) {
            Mail::to($email)->send(new AssessmentConfirmed($assessmentRequest));
        }

        return redirect()->route('enrol.submitted')->with('assessment_booked', true);
    }

    // -------------------------------------------------------------------------
    // Public (signed URL) slot booking — no auth required
    // -------------------------------------------------------------------------

    public function publicSelectSlot(AssessmentRequest $assessmentRequest)
    {
        abort_unless($assessmentRequest->status === 'slot_offered', 403, 'This booking link is no longer active.');

        session(['booking_token' => 'ar_' . $assessmentRequest->id]);

        $slots = $this->generateAvailableSlots(42);

        return view('enrolment.book-slot', compact('assessmentRequest', 'slots'));
    }

    public function publicBookSlot(Request $request, AssessmentRequest $assessmentRequest)
    {
        abort_unless(session('booking_token') === 'ar_' . $assessmentRequest->id, 403, 'Invalid or expired session.');

        $data = $request->validate(['slot_key' => 'required|string']);
        [$date, $startTime] = explode('|', $data['slot_key']);

        [$slot, $error] = $this->findOrCreateSlot($date, $startTime);
        abort_if($error, 422, $error ?? 'Slot not available.');

        $assessmentRequest->update(['assessment_slot_id' => $slot->id, 'status' => 'booked']);
        $assessmentRequest->load(['handler.user', 'dog', 'slot']);
        if ($email = $assessmentRequest->handler?->user?->email) {
            Mail::to($email)->send(new AssessmentConfirmed($assessmentRequest));
        }

        session()->forget('booking_token');

        return redirect()->route('enrol.submitted')->with('assessment_booked', true);
    }

    // -------------------------------------------------------------------------
    // Slot generation helpers
    // -------------------------------------------------------------------------

    private function generateAvailableSlots(int $days = 42): \Illuminate\Support\Collection
    {
        $slots          = collect();
        $availabilities = AssessmentAvailability::all();
        $specials       = AssessmentSpecialDate::where('date', '>', today())
            ->where('date', '<=', today()->addDays($days))
            ->orderBy('date')->get();

        // Weekly recurring
        for ($i = 1; $i <= $days; $i++) {
            $date = today()->addDays($i);
            if (!CalendarWeek::isWeekActive($date)) continue;
            if (!CalendarDay::isDayActive($date)) continue;

            foreach ($availabilities as $avail) {
                if ($avail->day_of_week == $date->dayOfWeek) {
                    $booked = AssessmentRequest::whereIn('status', ['booked', 'completed'])
                        ->whereHas('slot', fn($q) => $q->whereDate('date', $date->toDateString())->where('start_time', $avail->start_time))
                        ->count();
                    if ($booked < $avail->max_bookings) {
                        $slots->push((object)[
                            'date'         => $date->copy(),
                            'start_time'   => $avail->start_time,
                            'max_bookings' => $avail->max_bookings,
                            'notes'        => $avail->notes,
                            'remaining'    => $avail->max_bookings - $booked,
                            'key'          => $date->format('Y-m-d') . '|' . $avail->start_time,
                        ]);
                    }
                }
            }
        }

        // Special one-off dates
        foreach ($specials as $special) {
            if (!CalendarWeek::isWeekActive($special->date)) continue;
            if (!CalendarDay::isDayActive($special->date)) continue;
            if ($slots->contains(fn($s) => $s->date->isSameDay($special->date) && $s->start_time === $special->start_time)) continue;

            $booked = AssessmentRequest::whereIn('status', ['booked', 'completed'])
                ->whereHas('slot', fn($q) => $q->whereDate('date', $special->date->toDateString())->where('start_time', $special->start_time))
                ->count();
            if ($booked < $special->max_bookings) {
                $slots->push((object)[
                    'date'         => $special->date,
                    'start_time'   => $special->start_time,
                    'max_bookings' => $special->max_bookings,
                    'notes'        => $special->notes,
                    'remaining'    => $special->max_bookings - $booked,
                    'key'          => $special->date->format('Y-m-d') . '|' . $special->start_time,
                ]);
            }
        }

        return $slots->sortBy(fn($s) => $s->date->format('Y-m-d') . ' ' . $s->start_time)->values();
    }

    /**
     * Given a date + start_time, verify capacity and return a concrete AssessmentSlot (find or create).
     * Returns [$slot, $errorMessage] — $errorMessage is null on success.
     */
    private function findOrCreateSlot(string $date, string $startTime): array
    {
        $carbonDate = Carbon::parse($date);

        // Look up source for max_bookings
        $special = AssessmentSpecialDate::where('date', $date)->where('start_time', $startTime)->first();
        $avail   = AssessmentAvailability::where('day_of_week', $carbonDate->dayOfWeek)->where('start_time', $startTime)->first();
        $source  = $special ?? $avail;

        if (!$source) {
            return [null, 'This slot is no longer available.'];
        }

        // Check capacity
        $existing = AssessmentSlot::whereDate('date', $date)->where('start_time', $startTime)->first();
        $booked = AssessmentRequest::whereIn('status', ['booked', 'completed'])
            ->whereHas('slot', fn($q) => $q->whereDate('date', $date)->where('start_time', $startTime))
            ->count();

        if ($booked >= $source->max_bookings) {
            return [null, 'This slot is now fully booked — please choose another.'];
        }

        $slot = $existing ?? AssessmentSlot::create([
            'date'         => $date,
            'start_time'   => $startTime,
            'max_bookings' => $source->max_bookings,
            'notes'        => $source->notes,
            'is_available' => true,
        ]);

        return [$slot, null];
    }

    // -------------------------------------------------------------------------
    // Graduate enrolment form — signed URL, no auth required
    // -------------------------------------------------------------------------

    public function graduateForm(AssessmentRequest $assessmentRequest)
    {
        abort_unless($assessmentRequest->status === 'completed', 403, 'This enrolment link is no longer active.');

        session(['graduate_token' => 'ar_' . $assessmentRequest->id]);

        $assessmentRequest->load(['handler', 'dog']);

        return view('enrolment.graduate', compact('assessmentRequest'));
    }

    public function graduateStore(Request $request, AssessmentRequest $assessmentRequest)
    {
        abort_unless(session('graduate_token') === 'ar_' . $assessmentRequest->id, 403, 'Invalid or expired session.');

        $request->validate([
            'training_goal'    => 'required|in:competitive_dog_sport,chilled_canine_companion',
            'whatsapp_consent' => 'required|in:yes,no,unsure',
            'photo_consent'    => 'required|in:yes,no,unsure',
            'vaccination_card_path' => 'nullable|string|max:500',
        ]);

        $handler = $assessmentRequest->handler;
        $dog     = $assessmentRequest->dog;

        // Update handler consents
        $handler->update([
            'whatsapp_consent' => $request->whatsapp_consent,
            'photo_consent'    => $request->photo_consent,
        ]);

        // Update vaccination card if newly uploaded
        if ($request->filled('vaccination_card_path')) {
            $dog->update(['vaccination_card_path' => $request->vaccination_card_path]);
        }

        // Prevent duplicate pending enrolments
        $existing = Enrolment::where('handler_id', $handler->id)
            ->where('dog_id', $dog->id)
            ->whereNull('class_id')
            ->whereNotIn('status', ['withdrawn', 'rejected'])
            ->first();

        if (!$existing) {
            Enrolment::create([
                'handler_id'            => $handler->id,
                'dog_id'                => $dog->id,
                'class_id'              => null,
                'assessment_request_id' => $assessmentRequest->id,
                'status'                => 'pending',
                'pathway'               => 'assessment',
                'branch'                => 'honeydew',
                'enrolled_at'           => now(),
            ]);

            // Store training goal on handler
            $handler->update(['training_goal' => $request->training_goal]);

            // Notify admin
            $adminEmail = AppSetting::get('admin_email');
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new NewEnrolmentNotification($handler, $dog, null));
            }

            User::where('is_admin', true)->each(function (User $admin) use ($handler, $dog) {
                AppNotification::create([
                    'user_id' => $admin->id,
                    'type'    => 'new_enrolment',
                    'title'   => 'New Enrolment — Assessment Graduate',
                    'message' => $handler->first_name . ' ' . $handler->last_name . ' enrolled ' . $dog->name . ' (assessment graduate).',
                    'data'    => [],
                ]);
            });
        }

        session()->forget('graduate_token');

        // Pre-fill the forgot-password form and send them to activate their account
        session([
            'prefill_name'  => trim($handler->first_name . ' ' . $handler->last_name),
            'prefill_email' => $handler->user?->email ?? '',
        ]);

        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        session(['url.intended' => route('dashboard')]);
        return redirect()->route('password.request');
    }

    // -------------------------------------------------------------------------
    // Private lessons outcome — signed URL form (mirrors graduate enrolment)
    // -------------------------------------------------------------------------

    public function privateLessonsGate(AssessmentRequest $assessmentRequest)
    {
        abort_unless($assessmentRequest->status === 'completed', 403, 'This link is no longer active.');

        session(['private_lesson_token' => 'ar_' . $assessmentRequest->id]);

        $assessmentRequest->load(['handler', 'dog']);

        return view('enrolment.private-lessons-gate', compact('assessmentRequest'));
    }

    public function privateLessonsStore(Request $request, AssessmentRequest $assessmentRequest)
    {
        abort_unless(session('private_lesson_token') === 'ar_' . $assessmentRequest->id, 403, 'Invalid or expired session.');

        $request->validate([
            'training_goal'         => 'required|in:competitive_dog_sport,chilled_canine_companion',
            'whatsapp_consent'      => 'required|in:yes,no,unsure',
            'photo_consent'         => 'required|in:yes,no,unsure',
            'vaccination_card_path' => 'nullable|string|max:500',
        ]);

        $handler = $assessmentRequest->handler;
        $dog     = $assessmentRequest->dog;

        $handler->update([
            'training_goal'    => $request->training_goal,
            'whatsapp_consent' => $request->whatsapp_consent,
            'photo_consent'    => $request->photo_consent,
        ]);

        if ($request->filled('vaccination_card_path')) {
            $dog->update(['vaccination_card_path' => $request->vaccination_card_path]);
        }

        // Keep token so the book page can fire the in-app invite after login
        session(['private_lesson_ar_id' => $assessmentRequest->id]);
        session()->forget('private_lesson_token');

        if (auth()->check()) {
            $this->sendPrivateLessonInvite($assessmentRequest);
            return redirect()->route('handler.private-lessons.book');
        }

        // Pre-fill the auth forms with what we already know
        session([
            'prefill_name'  => trim($handler->first_name . ' ' . $handler->last_name),
            'prefill_email' => $handler->user?->email ?? '',
        ]);

        session(['url.intended' => route('handler.private-lessons.book')]);

        // The assessment form creates an account with a random password — the handler
        // has never set their own password, so send them to reset it to claim their account.
        return redirect()->route('password.request');
    }

    /**
     * Send a one-time in-app invite message to the handler encouraging them to book.
     * Idempotent: keyed on assessment request so it won't duplicate if called twice.
     */
    public function sendPrivateLessonInvite(AssessmentRequest $assessmentRequest): void
    {
        $handler = $assessmentRequest->handler;
        if (!$handler?->user) {
            return;
        }

        $dogName = $assessmentRequest->dog?->name ?? 'your dog';

        $adminUser = User::where('is_admin', true)->first();
        if (!$adminUser) {
            return;
        }

        app(MessageService::class)->createDirect(
            $adminUser->id,
            $handler->user->id,
            "Book Your Private Lesson — {$dogName}",
            [
                ['type' => 'text', 'content' => "Great news! Based on {$dogName}'s assessment, we recommend private lessons to help you both get the most out of training. You can book your first session directly through the app."],
                ['type' => 'button', 'label' => 'Book a Private Lesson', 'url' => route('handler.private-lessons.book')],
            ]
        );
    }
}
