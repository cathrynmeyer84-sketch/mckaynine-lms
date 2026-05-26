<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\{User, Handler, Dog, DogClass, ClassDate, Enrolment, ClassType};
use App\Models\Register;

/**
 * Seed an EO3 Term 2 2026 class with 9 dogs for instructor fee testing.
 *
 * Instructor split:
 *   Sarah (id 1) → 4 dogs  |  James (id 2) → 5 dogs
 *
 * 1 dog has multi_dog_discount (25% off course price)
 *
 * Attendance across 9 weekly sessions:
 *   5 dogs → 100%  (9/9 present)
 *   2 dogs →  90%  (8/9 – absent week 9)
 *   2 dogs →  70%  (6/9 – absent weeks 7, 8, 9)
 *
 * Course price: R2 000
 * Fee maths preview:
 *   avg_price   = (8 × R2000 + 1 × R1500) / 9  = R1 944.44
 *   weekly_rate = R1 944.44 / 9               = R  216.05
 *   Sarah: 36 dog-sessions × R216.05 × 0.40   = R3 114.27
 *   James: 37 dog-sessions × R216.05 × 0.40   = R3 201.48
 */
class EO3Term2FeeTestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Instructors ───────────────────────────────────────────────
        $sarah = \App\Models\Instructor::findOrFail(1);
        $james = \App\Models\Instructor::findOrFail(2);

        // ── Class type ────────────────────────────────────────────────
        $classType = ClassType::findOrFail(2); // Elementary Obedience 3

        // ── Create the class ─────────────────────────────────────────
        $startDate = Carbon::parse('2026-04-28'); // Tuesday, week 1
        $endDate   = Carbon::parse('2026-06-23'); // Tuesday, week 9

        $class = DogClass::create([
            'name'          => 'EO3 Term 2 2026',
            'class_type_id' => $classType->id,
            'has_final_exam'=> true,
            'max_capacity'  => 12,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'location'      => 'McKaynine Training Ground, Sandton',
            'course_price'  => 2000.00,
        ]);

        // Attach both instructors (Sarah lead)
        $class->instructors()->attach($sarah->id, ['is_lead' => true]);
        $class->instructors()->attach($james->id, ['is_lead' => false]);

        // ── Create 9 weekly class dates ───────────────────────────────
        $classDates = [];
        for ($week = 1; $week <= 9; $week++) {
            $classDates[$week] = ClassDate::create([
                'class_id'    => $class->id,
                'date'        => $startDate->copy()->addWeeks($week - 1),
                'start_time'  => '09:00',
                'end_time'    => '10:00',
                'week_number' => $week,
                'is_off_week' => false,
            ]);
        }

        // ── Dog definitions ───────────────────────────────────────────
        // [name, breed, instructor, discount, attendance_pattern]
        // attendance_pattern: 'all' | '90' | '70'
        $dogs = [
            // Sarah's 4 dogs
            ['Bella',   'Labrador Retriever',  $sarah, true,  'all'], // 25% discount
            ['Archie',  'Golden Retriever',    $sarah, false, 'all'],
            ['Luna',    'Border Collie',       $sarah, false, 'all'],
            ['Cooper',  'Beagle',              $sarah, false, 'all'],
            // James's 5 dogs
            ['Max',     'German Shepherd',     $james, false, 'all'],
            ['Daisy',   'Cocker Spaniel',      $james, false, '90'],
            ['Bruno',   'Rottweiler',          $james, false, '90'],
            ['Nala',    'Weimaraner',          $james, false, '70'],
            ['Oscar',   'Boxer',               $james, false, '70'],
        ];

        foreach ($dogs as $i => [$dogName, $breed, $instructor, $discount, $attendance]) {
            $n = $i + 1;

            // Handler user
            $user = User::create([
                'name'       => "Test Handler {$n}",
                'email'      => "fee-test-handler{$n}@mckaynine.test",
                'password'   => Hash::make('password'),
                'is_handler' => true,
                'is_active'  => true,
            ]);

            $handler = Handler::create([
                'user_id'              => $user->id,
                'first_name'           => "Handler{$n}",
                'last_name'            => 'FeeTest',
                'cell_number'          => '082 000 00' . str_pad($n, 2, '0', STR_PAD_LEFT),
                'status'               => 'active',
                'terms_agreed'         => true,
                'ground_rules_agreed'  => true,
            ]);

            $dog = Dog::create([
                'handler_id'        => $handler->id,
                'name'              => $dogName,
                'breed'             => $breed,
                'date_of_birth'     => Carbon::now()->subMonths(14),
                'gender_repro_status' => 'female_intact',
                'multi_dog_discount'  => $discount,
            ]);

            // Confirmed enrolment assigned to the right instructor
            $enrolment = Enrolment::create([
                'dog_id'                  => $dog->id,
                'handler_id'              => $handler->id,
                'class_id'                => $class->id,
                'status'                  => 'confirmed',
                'pathway'                 => 'assessment',
                'assigned_instructor_id'  => $instructor->id,
                'enrolled_at'             => Carbon::parse('2026-04-01'),
                'confirmed_at'            => Carbon::parse('2026-04-10'),
            ]);

            // ── Attendance records ────────────────────────────────────
            // absent_weeks: which week numbers the dog is absent for
            $absentWeeks = match ($attendance) {
                '90'    => [9],        // 1 absence → 8/9 = 88.9%
                '70'    => [7, 8, 9],  // 3 absences → 6/9 = 66.7%
                default => [],         // all present
            };

            foreach ($classDates as $week => $classDate) {
                Register::create([
                    'class_date_id' => $classDate->id,
                    'enrolment_id'  => $enrolment->id,
                    'attendance'    => in_array($week, $absentWeeks) ? 'absent' : 'present',
                    'marked_at'     => now(),
                ]);
            }
        }

        $this->command->info('✓ EO3 Term 2 2026 created with 9 dogs.');
        $this->command->info('  Sarah: 4 dogs (including 1 with 25% discount), all 9/9 sessions');
        $this->command->info('  James: 5 dogs — 1 at 100%, 2 at ~90%, 2 at ~70%');
        $this->command->info('');
        $this->command->info('  Expected fee output:');
        $this->command->info('  avg_price   = R1 944.44  |  weekly_rate = R216.05');
        $this->command->info('  Sarah: 36 dog-sessions → R3 114.27');
        $this->command->info('  James: 37 dog-sessions → R3 201.48');
        $this->command->info('  Grand total: R6 315.75');
    }
}
