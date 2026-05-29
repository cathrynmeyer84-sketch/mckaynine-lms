<?php

use App\Http\Controllers\AccountLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EnrolmentController;
use App\Http\Controllers\ClassInfoController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\AssessmentScoreController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Instructor;
use App\Http\Controllers\Handler;
use App\Http\Controllers\Auth\RoleSelectorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    $puppyClass   = \App\Models\ClassType::where('is_entry_class', true)->where('info_page_enabled', true)->first();
    $groupClasses = \App\Models\ClassType::where('is_entry_class', false)->where('info_page_enabled', true)->orderBy('name')->get();
    $branch       = \App\Models\BranchSetting::current();
    return view('welcome', compact('puppyClass', 'groupClasses', 'branch'));
});

// Post-login router: sends multi-role users to the role picker, single-role users straight through
Route::get('/dashboard', function () {
    $user  = auth()->user();
    $roles = collect(['admin', 'instructor', 'handler'])
        ->filter(fn($r) => match($r) {
            'admin'      => $user->is_admin,
            'instructor' => $user->is_instructor,
            'handler'    => $user->is_handler,
        });

    if ($roles->count() > 1) {
        return redirect()->route('auth.select-role');
    }

    if ($user->is_admin)      return redirect()->route('admin.dashboard');
    if ($user->is_instructor) return redirect()->route('instructor.dashboard');
    return redirect()->route('handler.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Role picker (only needed for multi-role users)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/select-role',  [RoleSelectorController::class, 'show'])  ->name('auth.select-role');
    Route::post('/select-role', [RoleSelectorController::class, 'select'])->name('auth.select-role.post');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Notifications
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Enrolment — public, no auth required
Route::prefix('enrol')->name('enrol.')->group(function () {
    Route::get('/', [EnrolmentController::class, 'start'])->name('start');
    Route::get('/puppy', [EnrolmentController::class, 'puppyForm'])->name('puppy');
    Route::post('/puppy', [EnrolmentController::class, 'storePuppy'])->name('puppy.store');
    Route::post('/upload-vaccination', [EnrolmentController::class, 'uploadVaccination'])->name('upload.vaccination');
    Route::get('/assessment', [EnrolmentController::class, 'assessmentForm'])->name('assessment');
    Route::post('/assessment', [EnrolmentController::class, 'storeAssessment'])->name('assessment.store');
    Route::get('/submitted', [EnrolmentController::class, 'submitted'])->name('submitted');
});

// Slot booking + dog picker — requires auth
Route::middleware('auth')->prefix('enrol')->name('enrol.')->group(function () {
    Route::get('/choose-dog', [EnrolmentController::class, 'chooseDog'])->name('choose-dog');
    Route::post('/existing-dog', [EnrolmentController::class, 'storeExistingDog'])->name('existing-dog.store');
    Route::get('/existing-dog/confirmed', [EnrolmentController::class, 'existingDogConfirmed'])->name('existing-dog.confirmed');
    Route::get('/slot/{assessmentRequest}', [EnrolmentController::class, 'selectSlot'])->name('slot');
    Route::post('/slot/{assessmentRequest}', [EnrolmentController::class, 'bookSlot'])->name('slot.book');
});

// Public slot booking — signed URL, no auth required
Route::prefix('enrol')->name('enrol.')->group(function () {
    Route::get('/book-slot/{assessmentRequest}', [EnrolmentController::class, 'publicSelectSlot'])
        ->name('public-slot')
        ->middleware('signed');
    Route::post('/book-slot/{assessmentRequest}', [EnrolmentController::class, 'publicBookSlot'])
        ->name('public-slot.book');
    Route::get('/graduate/{assessmentRequest}', [EnrolmentController::class, 'graduateForm'])
        ->name('graduate')
        ->middleware('signed');
    Route::post('/graduate/{assessmentRequest}', [EnrolmentController::class, 'graduateStore'])
        ->name('graduate.store');
    Route::get('/private-lessons/{assessmentRequest}', [EnrolmentController::class, 'privateLessonsGate'])
        ->name('private-lessons')
        ->middleware('signed');
    Route::post('/private-lessons/{assessmentRequest}', [EnrolmentController::class, 'privateLessonsStore'])
        ->name('private-lessons.store');
});

// Billing link approve/decline — no auth required, token-validated
Route::get('/billing/link/{token}/approve', [AccountLinkController::class, 'approve'])->name('billing.link.approve');
Route::get('/billing/link/{token}/decline', [AccountLinkController::class, 'decline'])->name('billing.link.decline');

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // ── Regular Admin ─────────────────────────────────────────────────────────
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Dogs
    Route::get('/dogs/{dog}', [Admin\DogController::class, 'show'])->name('dogs.show');
    Route::post('/dogs/{dog}/enrol', [Admin\EnrolmentController::class, 'storeForDog'])->name('dogs.enrol');
    Route::patch('/dogs/{dog}/multi-dog-discount', [Admin\DogController::class, 'toggleMultiDogDiscount'])->name('dogs.multi-dog-discount');

    // Handlers
    Route::get('/handlers', [Admin\HandlerController::class, 'index'])->name('handlers.index');
    Route::get('/handlers/{handler}', [Admin\HandlerController::class, 'show'])->name('handlers.show');
    Route::patch('/handlers/{handler}/status', [Admin\HandlerController::class, 'updateStatus'])->name('handlers.status');
    Route::get('/handlers/{handler}/assign-class', [Admin\HandlerController::class, 'assignClassForm'])->name('handlers.assign-class');
    Route::post('/handlers/{handler}/assign-class', [Admin\HandlerController::class, 'assignClass'])->name('handlers.assign-class.store');

    // Enrolments
    Route::get('/enrolments', [Admin\EnrolmentController::class, 'index'])->name('enrolments.index');
    Route::get('/enrolments/{enrolment}', [Admin\EnrolmentController::class, 'show'])->name('enrolments.show');
    Route::post('/enrolments/{enrolment}/confirm', [Admin\EnrolmentController::class, 'confirm'])->name('enrolments.confirm');
    Route::post('/enrolments/{enrolment}/request-vet-clearance', [Admin\EnrolmentController::class, 'requestVetClearance'])->name('enrolments.request-vet-clearance');
    Route::post('/enrolments/{enrolment}/reject', [Admin\EnrolmentController::class, 'reject'])->name('enrolments.reject');
    Route::post('/enrolments/{enrolment}/assign-class', [Admin\EnrolmentController::class, 'assignClass'])->name('enrolments.assign-class');
    Route::post('/enrolments/{enrolment}/confirm-class', [Admin\EnrolmentController::class, 'confirmClass'])->name('enrolments.confirm-class');
    Route::patch('/enrolments/{enrolment}/assign-instructor', [Admin\EnrolmentController::class, 'assignInstructor'])->name('enrolments.assign-instructor');

    // Classes
    Route::resource('classes', Admin\ClassController::class);
    Route::get('/classes/{class}/register', [Admin\ClassController::class, 'viewRegister'])->name('classes.register');
    Route::get('/classes/{class}/content-schedule', [Admin\ClassController::class, 'contentSchedule'])->name('classes.content-schedule');
    Route::post('/classes/{class}/content-schedule', [Admin\ClassController::class, 'saveContentSchedule'])->name('classes.content-schedule.save');
    Route::get('/classes/{class}/info-page', [Admin\ClassController::class, 'editInfoPage'])->name('classes.info-page');
    Route::put('/classes/{class}/info-page', [Admin\ClassController::class, 'updateInfoPage'])->name('classes.info-page.update');
    Route::post('/classes/{class}/mark-complete', [Admin\ClassController::class, 'markComplete'])->name('classes.mark-complete');
    Route::patch('/classes/{class}/dates/{classDate}/stand-in', [Admin\ClassController::class, 'setStandIn'])->name('classes.dates.stand-in');

    // Assessments
    Route::get('/assessments', [Admin\AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/settings', [Admin\AssessmentController::class, 'settings'])->name('assessments.settings');
    Route::post('/assessments/settings', [Admin\AssessmentController::class, 'updateSettings'])->name('assessments.settings.save');
    Route::get('/assessments/slots', [Admin\AssessmentController::class, 'manageSlots'])->name('assessments.slots');
    Route::post('/assessments/availabilities', [Admin\AssessmentController::class, 'storeAvailability'])->name('assessments.availabilities.store');
    Route::delete('/assessments/availabilities/{availability}', [Admin\AssessmentController::class, 'deleteAvailability'])->name('assessments.availabilities.delete');
    Route::post('/assessments/special-dates', [Admin\AssessmentController::class, 'storeSpecialDate'])->name('assessments.special-dates.store');
    Route::delete('/assessments/special-dates/{specialDate}', [Admin\AssessmentController::class, 'deleteSpecialDate'])->name('assessments.special-dates.delete');
    Route::get('/assessments/{assessmentRequest}', [Admin\AssessmentController::class, 'show'])->name('assessments.show');
    Route::post('/assessments/{assessmentRequest}/send-booking-link', [Admin\AssessmentController::class, 'sendBookingLink'])->name('assessments.send-booking-link');
    Route::get('/assessments/{assessmentRequest}/score', [Admin\AssessmentController::class, 'scoreForm'])->name('assessments.score');
    Route::post('/assessments/{assessmentRequest}/score', [Admin\AssessmentController::class, 'storeScore'])->name('assessments.score.store');
    Route::post('/assessments/{assessmentScore}/release', [Admin\AssessmentController::class, 'releaseOutcome'])->name('assessments.release');

    // Inbox — specific routes MUST come before the {conversation} wildcard
    Route::get('/inbox', [Admin\InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/compose', [Admin\InboxController::class, 'create'])->name('inbox.compose');
    Route::post('/inbox', [Admin\InboxController::class, 'store'])->name('inbox.store');
    Route::get('/inbox/templates', [Admin\InboxController::class, 'templates'])->name('inbox.templates.index');
    Route::get('/inbox/templates/{template}/edit', [Admin\InboxController::class, 'editTemplate'])->name('inbox.templates.edit');
    Route::post('/inbox/templates/{template}', [Admin\InboxController::class, 'updateTemplate'])->name('inbox.templates.update');
    Route::get('/inbox/{conversation}', [Admin\InboxController::class, 'show'])->name('inbox.show');
    Route::post('/inbox/{conversation}/reply', [Admin\InboxController::class, 'reply'])->name('inbox.reply');

    // Results
    Route::get('/results', [Admin\ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{examResult}', [Admin\ResultController::class, 'show'])->name('results.show');
    Route::get('/results/{examResult}/edit', [Admin\ResultController::class, 'edit'])->name('results.edit');
    Route::put('/results/{examResult}', [Admin\ResultController::class, 'update'])->name('results.update');
    Route::post('/results/{examResult}/release', [Admin\ResultController::class, 'release'])->name('results.release');

    // Instructors
    Route::resource('instructors', Admin\InstructorController::class)->except(['destroy']);

    // Private lessons
    Route::get('/private-lessons', [Admin\PrivateLessonController::class, 'index'])->name('private-lessons.index');
    Route::get('/private-lessons/{lesson}', [Admin\PrivateLessonController::class, 'show'])->name('private-lessons.show');

    // ── Super Admin only ──────────────────────────────────────────────────────
    Route::middleware('super_admin')->group(function () {

        // Branch Settings
        Route::get('/settings/branch', [Admin\BranchSettingController::class, 'edit'])->name('branch-settings.edit');
        Route::put('/settings/branch', [Admin\BranchSettingController::class, 'update'])->name('branch-settings.update');

        // Email Templates
        Route::get('/email-templates', [Admin\EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('/email-templates/{emailTemplate}/edit', [Admin\EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('/email-templates/{emailTemplate}', [Admin\EmailTemplateController::class, 'update'])->name('email-templates.update');

        // Class Types + Grading setup
        Route::resource('class-types', Admin\ClassTypeController::class);
        Route::get('/class-types/{classType}/info-page', [Admin\ClassTypeController::class, 'editInfoPage'])->name('class-types.info-page.edit');
        Route::post('/class-types/{classType}/info-page', [Admin\ClassTypeController::class, 'updateInfoPage'])->name('class-types.info-page.update');
        Route::post('/class-types/{classType}/weeks/{week}', [Admin\ClassTypeController::class, 'saveWeek'])->name('class-types.weeks.save');
        Route::post('/class-types/{classType}/weeks/{week}/briefing', [Admin\ClassTypeController::class, 'storeBriefingItem'])->name('class-types.briefing.store');
        Route::post('/briefing-items/{item}', [Admin\ClassTypeController::class, 'updateBriefingItem'])->name('briefing-items.update');
        Route::delete('/briefing-items/{item}', [Admin\ClassTypeController::class, 'destroyBriefingItem'])->name('briefing-items.destroy');
        Route::post('/class-types/{classType}/grading', [Admin\ClassTypeController::class, 'storeGradingExercise'])->name('class-types.grading.store');
        Route::post('/grading-exercises/{exercise}/reorder', [Admin\ClassTypeController::class, 'reorderGradingExercise'])->name('grading-exercises.reorder');
        Route::post('/grading-exercises/{exercise}', [Admin\ClassTypeController::class, 'updateGradingExercise'])->name('grading-exercises.update');
        Route::delete('/grading-exercises/{exercise}', [Admin\ClassTypeController::class, 'destroyGradingExercise'])->name('grading-exercises.destroy');
        Route::post('/grading-exercises/{exercise}/events', [Admin\ClassTypeController::class, 'storeDeductionEvent'])->name('grading-exercises.events.store');
        Route::post('/grading-events/{event}', [Admin\ClassTypeController::class, 'updateDeductionEvent'])->name('grading-events.update');
        Route::delete('/grading-events/{event}', [Admin\ClassTypeController::class, 'destroyDeductionEvent'])->name('grading-events.destroy');
        Route::post('/grading-exercises/{exercise}/ratings', [Admin\ClassTypeController::class, 'storeRatingScale'])->name('grading-exercises.ratings.store');
        Route::post('/grading-ratings/{scale}', [Admin\ClassTypeController::class, 'updateRatingScale'])->name('grading-ratings.update');
        Route::delete('/grading-ratings/{scale}', [Admin\ClassTypeController::class, 'destroyRatingScale'])->name('grading-ratings.destroy');

        // Resources (all)
        Route::get('/resources', [Admin\ResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/create', [Admin\ResourceController::class, 'create'])->name('resources.create');
        Route::post('/resources', [Admin\ResourceController::class, 'store'])->name('resources.store');
        Route::get('/resources/{resource}', [Admin\ResourceController::class, 'show'])->name('resources.show');
        Route::get('/resources/{resource}/edit', [Admin\ResourceController::class, 'edit'])->name('resources.edit');
        Route::put('/resources/{resource}', [Admin\ResourceController::class, 'update'])->name('resources.update');
        Route::patch('/resources/{resource}/toggle', [Admin\ResourceController::class, 'toggle'])->name('resources.toggle');

        // Calendar (all)
        Route::get('/calendar', [Admin\CalendarController::class, 'index'])->name('calendar.index');
        Route::post('/calendar/day', [Admin\CalendarController::class, 'saveDay'])->name('calendar.day.save');

        // Billing (all)
        Route::post('/handlers/{handler}/payment', [Admin\BillingController::class, 'recordPayment'])->name('handlers.billing.payment');
        Route::post('/handlers/{handler}/invoice', [Admin\BillingController::class, 'createInvoice'])->name('handlers.billing.invoice');
        Route::get('/billing/pops', [Admin\BillingController::class, 'pendingPops'])->name('billing.pops');
        Route::post('/billing/pops/{billingPop}/review', [Admin\BillingController::class, 'reviewPop'])->name('billing.pops.review');
        Route::get('/billing/pops/{billingPop}/download', [Admin\BillingController::class, 'downloadPop'])->name('billing.pops.download');

        // Instructor Fees
        Route::get('/fees', [Admin\FeeController::class, 'index'])->name('fees.index');
        Route::post('/fees/statements/release', [Admin\FeeStatementController::class, 'release'])->name('fees.statements.release');
        Route::patch('/fees/statements/{statement}/pay', [Admin\FeeStatementController::class, 'pay'])->name('fees.statements.pay');
        Route::patch('/fees/statements/{statement}/unpay', [Admin\FeeStatementController::class, 'unpay'])->name('fees.statements.unpay');

        // Billing config (IO setup)
        Route::patch('/handlers/{handler}/client-id', [Admin\BillingController::class, 'saveClientId'])->name('handlers.billing.client-id');

        // Calendar settings
        Route::post('/calendar/settings', [Admin\CalendarController::class, 'saveSettings'])->name('calendar.settings.save');

        // School years
        Route::post('/calendar/school-years', [Admin\CalendarController::class, 'storeSchoolYear'])->name('calendar.school-years.store');
        Route::put('/calendar/school-years/{schoolYear}', [Admin\CalendarController::class, 'updateSchoolYear'])->name('calendar.school-years.update');
        Route::delete('/calendar/school-years/{schoolYear}', [Admin\CalendarController::class, 'destroySchoolYear'])->name('calendar.school-years.destroy');

        // Document library
        Route::get('/documents', [Admin\DocumentLibraryController::class, 'index'])->name('documents.index');
        Route::post('/documents', [Admin\DocumentLibraryController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{document}', [Admin\DocumentLibraryController::class, 'destroy'])->name('documents.destroy');

        // Invitations
        Route::get('/invitations', [Admin\InvitationController::class, 'index'])->name('invitations.index');
        Route::post('/invitations', [Admin\InvitationController::class, 'store'])->name('invitations.store');
        Route::post('/invitations/csv', [Admin\InvitationController::class, 'storeCsv'])->name('invitations.csv');
        Route::get('/invitations/sample-csv', [Admin\InvitationController::class, 'sampleCsv'])->name('invitations.sample-csv');
        Route::post('/invitations/{invitation}/resend', [Admin\InvitationController::class, 'resend'])->name('invitations.resend');
        Route::delete('/invitations/{invitation}', [Admin\InvitationController::class, 'destroy'])->name('invitations.destroy');

    }); // end super_admin

});

// Instructor routes
Route::middleware(['auth', 'verified'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/', [Instructor\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [Instructor\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [Instructor\ProfileController::class, 'update'])->name('profile.update');

    // Classes
    Route::get('/classes', [Instructor\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/{class}', [Instructor\ClassController::class, 'show'])->name('classes.show');
    Route::get('/classes/{class}/enrolments/{enrolment}', [Instructor\ClassController::class, 'showDog'])->name('classes.dog');

    // Register
    Route::get('/classes/{class}/register/{classDate}', [Instructor\RegisterController::class, 'show'])->name('register.show');
    Route::post('/classes/{class}/register/{classDate}', [Instructor\RegisterController::class, 'store'])->name('register.store');

    // Grading
    Route::get('/classes/{class}/grade', [Instructor\GradeController::class, 'index'])->name('grade.index');
    Route::get('/classes/{class}/grade/exercise/{exercise}', [Instructor\GradeController::class, 'exerciseView'])->name('grade.exercise');
    Route::post('/classes/{class}/grade/exercise/{exercise}/{enrolment}', [Instructor\GradeController::class, 'saveExercise'])->name('grade.exercise.save');
    Route::get('/classes/{class}/grade/{enrolment}', [Instructor\GradeController::class, 'form'])->name('grade.form');
    Route::post('/classes/{class}/grade/{enrolment}', [Instructor\GradeController::class, 'store'])->name('grade.store');
    Route::get('/classes/{class}/grade/{enrolment}/cgc-bronze', [Instructor\GradeController::class, 'cgcBronzeForm'])->name('grade.cgc-bronze');
    Route::post('/classes/{class}/grade/{enrolment}/cgc-bronze', [Instructor\GradeController::class, 'storeCgcBronze'])->name('grade.cgc-bronze.store');
    Route::get('/classes/{class}/grade/{enrolment}/eo', [Instructor\GradeController::class, 'eoForm'])->name('grade.eo');
    Route::post('/classes/{class}/grade/{enrolment}/eo', [Instructor\GradeController::class, 'storeEo'])->name('grade.eo.store');
    Route::post('/classes/{class}/grade/{enrolment}/submit', [Instructor\GradeController::class, 'submitForReview'])->name('grade.submit');

    // Inbox
    Route::get('/inbox', [Instructor\InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/{conversation}', [Instructor\InboxController::class, 'show'])->name('inbox.show');
    Route::post('/inbox/{conversation}/reply', [Instructor\InboxController::class, 'reply'])->name('inbox.reply');

    // Assessment scoring
    Route::get('/assessments/{assessmentRequest}/score', [AssessmentScoreController::class, 'form'])->name('assessment.score');
    Route::post('/assessments/{assessmentRequest}/score', [AssessmentScoreController::class, 'store'])->name('assessment.score.store');

    // Week content preview
    Route::get('/classes/{class}/content/{classDate}', [Instructor\ClassController::class, 'showWeekContent'])->name('classes.week-content');
    // Week briefing
    Route::get('/classes/{class}/briefing/{classDate}', [Instructor\ClassController::class, 'showWeekBriefing'])->name('classes.week-briefing');

    // Goals
    Route::post('/classes/{class}/goals', [Instructor\ClassController::class, 'storeGoal'])->name('classes.goals.store');
    Route::patch('/goals/{goal}', [Instructor\ClassController::class, 'updateGoal'])->name('goals.update');

    // Private lessons
    Route::get('/private-lessons', [Instructor\PrivateLessonController::class, 'index'])->name('private-lessons.index');
    Route::get('/private-lessons/availability', [Instructor\PrivateLessonController::class, 'availability'])->name('private-lessons.availability');
    Route::post('/private-lessons/availability', [Instructor\PrivateLessonController::class, 'availability'])->name('private-lessons.availability.save');
    Route::post('/private-lessons/opt-in', [Instructor\PrivateLessonController::class, 'toggleOptIn'])->name('private-lessons.opt-in');
    Route::get('/private-lessons/requests', [Instructor\PrivateLessonController::class, 'requests'])->name('private-lessons.requests');
    Route::post('/private-lessons/{lesson}/confirm', [Instructor\PrivateLessonController::class, 'confirm'])->name('private-lessons.confirm');
    Route::post('/private-lessons/{lesson}/reject', [Instructor\PrivateLessonController::class, 'reject'])->name('private-lessons.reject');
    Route::post('/private-lessons/{lesson}/reschedule', [Instructor\PrivateLessonController::class, 'requestReschedule'])->name('private-lessons.reschedule');
    Route::post('/private-lessons/{lesson}/complete', [Instructor\PrivateLessonController::class, 'complete'])->name('private-lessons.complete');
    Route::post('/private-lessons/blocks', [Instructor\PrivateLessonController::class, 'storeBlock'])->name('private-lessons.blocks.store');
    Route::delete('/private-lessons/blocks/{block}', [Instructor\PrivateLessonController::class, 'deleteBlock'])->name('private-lessons.blocks.destroy');

    // Fees
    Route::get('/fees', [Instructor\FeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/{statement}', [Instructor\FeeController::class, 'show'])->name('fees.show');
});

// Enrolment form 2 — post-assessment class enrolment
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/enrol/complete', [Handler\EnrolmentFormController::class, 'show'])->name('enrol.complete');
    Route::post('/enrol/complete', [Handler\EnrolmentFormController::class, 'store'])->name('enrol.complete.store');
    Route::post('/enrol/upload-dog-photo', [Handler\EnrolmentFormController::class, 'uploadDogPhoto'])->name('enrol.upload.dog-photo');
});

// Handler routes
Route::middleware(['auth', 'verified'])->prefix('my')->name('handler.')->group(function () {
    Route::get('/handlerdashboard', [Handler\DashboardController::class, 'index'])->name('dashboard');

    // Classes
    Route::get('/classes', [Handler\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/{enrolment}', [Handler\ClassController::class, 'show'])->name('classes.show');
    Route::get('/classes/{enrolment}/week/{classDate}', [Handler\ClassController::class, 'weekContent'])->name('classes.week');

    // Dogs
    Route::get('/dogs', [Handler\DogController::class, 'index'])->name('dogs.index');
    Route::get('/dogs/{dog}/edit', [Handler\DogController::class, 'edit'])->name('dogs.edit');
    Route::patch('/dogs/{dog}', [Handler\DogController::class, 'update'])->name('dogs.update');

    // Achievements
    Route::get('/achievements', [Handler\AchievementController::class, 'index'])->name('achievements.index');

    // Inbox
    Route::get('/inbox', [Handler\InboxController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/compose', [Handler\InboxController::class, 'create'])->name('inbox.compose');
    Route::post('/inbox', [Handler\InboxController::class, 'store'])->name('inbox.store');
    Route::get('/inbox/{conversation}', [Handler\InboxController::class, 'show'])->name('inbox.show');
    Route::post('/inbox/{conversation}/reply', [Handler\InboxController::class, 'reply'])->name('inbox.reply');

    // Resources
    Route::get('/resources', [Handler\ResourceController::class, 'index'])->name('handler.resources.index');
    Route::get('/resources/{resource}', [Handler\ResourceController::class, 'show'])->name('resources.show');

    // Survey
    Route::get('/survey/{enrolment}', [Handler\ClassController::class, 'surveyForm'])->name('survey.form');
    Route::post('/survey/{enrolment}', [Handler\ClassController::class, 'storeSurvey'])->name('survey.store');

    // Vet clearance upload
    Route::get('/vet-clearance/{enrolment}', [Handler\VetClearanceController::class, 'show'])->name('vet-clearance.upload');
    Route::post('/vet-clearance/{enrolment}', [Handler\VetClearanceController::class, 'upload'])->name('vet-clearance.upload.store');

    // Calendar (off days / school breaks)
    Route::get('/calendar', [Handler\CalendarController::class, 'index'])->name('calendar');

    // Billing portal
    Route::get('/billing', [Handler\BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/pop', [Handler\BillingController::class, 'uploadPop'])->name('billing.pop.upload');

    // Private lessons
    Route::get('/private-lessons', [Handler\PrivateLessonController::class, 'index'])->name('private-lessons.index');
    Route::get('/private-lessons/book', [Handler\PrivateLessonController::class, 'browse'])->name('private-lessons.book');
    Route::get('/private-lessons/slots/{instructor}', [Handler\PrivateLessonController::class, 'slots'])->name('private-lessons.slots');
    Route::post('/private-lessons', [Handler\PrivateLessonController::class, 'store'])->name('private-lessons.store');
    Route::post('/private-lessons/{lesson}/cancel', [Handler\PrivateLessonController::class, 'cancel'])->name('private-lessons.cancel');
});

// Fix: resources route needs different name
Route::middleware(['auth', 'verified'])->get('/my/resources', [Handler\ResourceController::class, 'index'])->name('handler.resources.index');

// Push subscriptions
Route::middleware(['auth'])->group(function () {
    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
});

// Public class info pages (class types and class instances)
Route::get('/classes/{slug}', [ClassInfoController::class, 'show'])->name('class-info.show');
Route::get('/classes/{slug}/enquire', [ClassInfoController::class, 'enquireForm'])->name('class-info.enquire.form');
Route::post('/classes/{slug}/enquire', [ClassInfoController::class, 'enquire'])->name('class-info.enquire');
Route::get('/classes/{typeSlug}/{class}', [ClassInfoController::class, 'showForClass'])->name('class-info.class')->where('class', '[0-9]+');

// Vet clearance PDF download (public — linked in email)
Route::get('/vet-clearance-form.pdf', function () {
    $path = storage_path('app/public/vet-clearance-form.pdf');
    if (file_exists($path)) {
        return response()->download($path, 'McKaynine-Vet-Clearance-Form.pdf');
    }
    abort(404, 'Vet clearance form not uploaded yet. Please contact McKaynine directly.');
})->name('vet-clearance.pdf');

// ── Invitation sign-up (public, no auth required) ─────────────
use App\Http\Controllers\InvitationRegisterController;
Route::get('/invite/{token}', [InvitationRegisterController::class, 'show'])->name('invitation.register');
Route::post('/invite/{token}', [InvitationRegisterController::class, 'store'])->name('invitation.register.store');

require __DIR__.'/auth.php';
