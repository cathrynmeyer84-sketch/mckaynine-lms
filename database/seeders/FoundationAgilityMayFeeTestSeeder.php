<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\{User, Handler, Dog, DogClass, ClassDate, Enrolment, ClassType};

/**
 * Seed a Foundation Agility May 2026 class (ongoing/monthly) for instructor fee testing.
 *
 * Instructor split:
 *   Sarah (id 1) → 3 dogs  |  James (id 2) → 2 dogs
 *
 * 2 dogs have multi_dog_discount (25% off course price):
 *   1 of Sarah's  |  1 of James's
 *
 * All 5 dogs confirmed-enrolled for May 2026.
 *
 * Course price (monthly): R1 500
 *
 * Fee maths (ongoing = flat per dog, no per-session rate):
 *   Full price:      R1 500 × 1.00 × 0.40 = R600 per dog
 *   Discounted:      R1 500 × 0.75 × 0.40 = R450 per dog
 *
 *   Sarah: 1 discounted + 2 full = R450 + R600 + R600 = R1 650
 *   James: 1 discounted + 1 full = R450 + R600          = R1 050
 *   Grand total: R2 700
 */
class FoundationAgilityMayFeeTestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Instructors ───────────────────────────────────────────────
        $sarah = \App\Models\Instructor::findOrFail(1);
        $james = \App\Models\Instructor::findOrFail(2);

        // ── Class type ────────────────────────────────────────────────
        $classType = ClassType::findOrFail(5); // Foundation Agility (ongoing/monthly)

        // ── Create the class ─────────────────────────────────────────
        // Ongoing class — starts before May, runs indefinitely
        $class = DogClass::create([
            'name'          => 'Foundation Agility May 2026',
            'class_type_id' => $classType->id,
            'has_final_exam'=> false,
            'max_capacity'  => 8,
            'start_date'    => Carbon::parse('2026-05-03'), // first Saturday in May
            'end_date'      => Carbon::parse('2026-12-31'), // ongoing through year
            'location'      => 'McKaynine Training Ground, Sandton',
            'course_price'  => 1500.00,
        ]);

        // Attach both instructors (Sarah lead)
        $class->instructors()->attach($sarah->id, ['is_lead' => true]);
        $class->instructors()->attach($james->id, ['is_lead' => false]);

        // ── Create May class dates (4 Saturdays) ──────────────────────
        $saturdays = [
            Carbon::parse('2026-05-03'),
            Carbon::parse('2026-05-10'),
            Carbon::parse('2026-05-17'),
            Carbon::parse('2026-05-24'),
        ];

        foreach ($saturdays as $i => $date) {
            ClassDate::create([
                'class_id'    => $class->id,
                'date'        => $date,
                'start_time'  => '08:00',
                'end_time'    => '09:00',
                'week_number' => $i + 1,
                'is_off_week' => false,
            ]);
        }

        // ── Dog definitions ───────────────────────────────────────────
        // [name, breed, instructor, multi_dog_discount]
        $dogs = [
            // Sarah's 3 dogs
            ['Zara',   'Belgian Malinois',    $sarah, true],  // 25% discount
            ['Pixel',  'Border Collie',       $sarah, false],
            ['Ember',  'Australian Shepherd', $sarah, false],
            // James's 2 dogs
            ['Titan',  'Doberman',            $james, true],  // 25% discount
            ['Scout',  'Vizsla',              $james, false],
        ];

        foreach ($dogs as $i => [$dogName, $breed, $instructor, $discount]) {
            $n = $i + 1;

            $user = User::create([
                'name'       => "Agility Test Handler {$n}",
                'email'      => "agility-fee-test-handler{$n}@mckaynine.test",
                'password'   => Hash::make('password'),
                'is_handler' => true,
                'is_active'  => true,
            ]);

            $handler = Handler::create([
                'user_id'             => $user->id,
                'first_name'          => "AgiHandler{$n}",
                'last_name'           => 'FeeTest',
                'cell_number'         => '083 000 00' . str_pad($n, 2, '0', STR_PAD_LEFT),
                'status'              => 'active',
                'terms_agreed'        => true,
                'ground_rules_agreed' => true,
            ]);

            $dog = Dog::create([
                'handler_id'          => $handler->id,
                'name'                => $dogName,
                'breed'               => $breed,
                'date_of_birth'       => Carbon::now()->subMonths(18),
                'gender_repro_status' => 'female_intact',
                'multi_dog_discount'  => $discount,
            ]);

            Enrolment::create([
                'dog_id'                 => $dog->id,
                'handler_id'             => $handler->id,
                'class_id'               => $class->id,
                'status'                 => 'confirmed',
                'pathway'                => 'assessment',
                'assigned_instructor_id' => $instructor->id,
                'enrolled_at'            => Carbon::parse('2026-04-28'),
                'confirmed_at'           => Carbon::parse('2026-04-30'),
            ]);
        }

        $this->command->info('✓ Foundation Agility May 2026 created with 5 dogs.');
        $this->command->info('  Sarah: 3 dogs (1 with 25% discount)');
        $this->command->info('  James: 2 dogs (1 with 25% discount)');
        $this->command->info('');
        $this->command->info('  Monthly fee (ongoing — flat per dog, no per-session rate):');
        $this->command->info('  Full price:    R1 500 × 1.00 × 0.40 = R600/dog');
        $this->command->info('  Discounted:    R1 500 × 0.75 × 0.40 = R450/dog');
        $this->command->info('');
        $this->command->info('  Sarah: R450 + R600 + R600 = R1 650');
        $this->command->info('  James: R450 + R600        = R1 050');
        $this->command->info('  Grand total: R2 700');
        $this->command->info('');
        $this->command->info('  → Check: Admin → Instructor Fees → Monthly → May 2026');
    }
}
