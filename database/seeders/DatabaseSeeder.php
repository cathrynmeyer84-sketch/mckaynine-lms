<?php

namespace Database\Seeders;

use App\Models\{User, Handler, Instructor, DogClass, Dog, ClassDate, Enrolment, WeeklyContent, Resource, AssessmentSlot};
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'McKaynine Admin',
            'email' => 'admin@mckaynine.co.za',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_instructor' => false,
            'is_handler' => false,
        ]);

        // Instructor users
        $instructorUser1 = User::create([
            'name' => 'Sarah van der Berg',
            'email' => 'sarah@mckaynine.co.za',
            'password' => bcrypt('password'),
            'is_instructor' => true,
            'is_handler' => false,
        ]);
        $instructor1 = Instructor::create([
            'user_id' => $instructorUser1->id,
            'first_name' => 'Sarah',
            'last_name' => 'van der Berg',
            'email' => 'sarah@mckaynine.co.za',
            'phone' => '082 555 0001',
            'bio' => 'Certified dog trainer with 10 years experience.',
        ]);

        $instructorUser2 = User::create([
            'name' => 'James Mthembu',
            'email' => 'james@mckaynine.co.za',
            'password' => bcrypt('password'),
            'is_instructor' => true,
            'is_handler' => false,
        ]);
        $instructor2 = Instructor::create([
            'user_id' => $instructorUser2->id,
            'first_name' => 'James',
            'last_name' => 'Mthembu',
            'email' => 'james@mckaynine.co.za',
            'phone' => '082 555 0002',
            'bio' => 'CGC and Rally specialist.',
        ]);

        // Handler users
        $handlerUser1 = User::create([
            'name' => 'Emma Thompson',
            'email' => 'emma@example.com',
            'password' => bcrypt('password'),
            'is_handler' => true,
        ]);
        $handler1 = Handler::create([
            'user_id' => $handlerUser1->id,
            'first_name' => 'Emma',
            'last_name' => 'Thompson',
            'cell_number' => '083 222 1111',
            'vet_name_location' => 'Fourways Vet Clinic',
            'status' => 'active',
            'terms_agreed' => true,
            'ground_rules_agreed' => true,
        ]);
        $dog1 = Dog::create([
            'handler_id' => $handler1->id,
            'name' => 'Biscuit',
            'date_of_birth' => Carbon::now()->subMonths(10),
            'breed' => 'Golden Retriever',
            'gender_repro_status' => 'female_intact',
        ]);

        $handlerUser2 = User::create([
            'name' => 'David Nkosi',
            'email' => 'david@example.com',
            'password' => bcrypt('password'),
            'is_handler' => true,
        ]);
        $handler2 = Handler::create([
            'user_id' => $handlerUser2->id,
            'first_name' => 'David',
            'last_name' => 'Nkosi',
            'cell_number' => '071 333 2222',
            'vet_name_location' => 'Sandton Animal Hospital',
            'status' => 'active',
            'terms_agreed' => true,
            'ground_rules_agreed' => true,
        ]);
        $dog2 = Dog::create([
            'handler_id' => $handler2->id,
            'name' => 'Zeus',
            'date_of_birth' => Carbon::now()->subMonths(14),
            'breed' => 'Border Collie',
            'gender_repro_status' => 'male_intact',
        ]);

        // Pending handler (awaiting admin review)
        $handlerUser3 = User::create([
            'name' => 'Chloe Pretorius',
            'email' => 'chloe@example.com',
            'password' => bcrypt('password'),
            'is_handler' => true,
        ]);
        $handler3 = Handler::create([
            'user_id' => $handlerUser3->id,
            'first_name' => 'Chloe',
            'last_name' => 'Pretorius',
            'cell_number' => '072 444 3333',
            'vet_name_location' => 'Centurion Pet Clinic',
            'status' => 'pending',
            'terms_agreed' => true,
            'ground_rules_agreed' => true,
        ]);
        $dog3 = Dog::create([
            'handler_id' => $handler3->id,
            'name' => 'Pepper',
            'date_of_birth' => Carbon::now()->subMonths(2)->subDays(15),
            'breed' => 'Jack Russell Terrier',
            'gender_repro_status' => 'female_intact',
        ]);

        // Create classes
        $puppyClass = DogClass::create([
            'name' => 'Puppy Class — Spring 2026',
            'category' => 'puppy_class',
            'has_final_exam' => false,
            'max_capacity' => 8,
            'start_date' => Carbon::now()->addDays(7),
            'end_date' => Carbon::now()->addDays(7 + 42),
            'status' => 'upcoming',
            'location' => 'McKaynine Training Ground, Sandton',
        ]);
        $puppyClass->instructors()->attach($instructor1->id, ['is_lead' => true]);

        $eoClass = DogClass::create([
            'name' => 'Elementary Obedience (3-month) — Autumn 2026',
            'category' => 'elementary_obedience_3m',
            'has_final_exam' => true,
            'max_capacity' => 10,
            'start_date' => Carbon::now()->subDays(14),
            'end_date' => Carbon::now()->addDays(70),
            'status' => 'active',
            'location' => 'McKaynine Training Ground, Sandton',
        ]);
        $eoClass->instructors()->attach($instructor1->id, ['is_lead' => true]);

        $cgcClass = DogClass::create([
            'name' => 'CGC Bronze — Autumn 2026',
            'category' => 'cgc_bronze',
            'has_final_exam' => true,
            'max_capacity' => 8,
            'start_date' => Carbon::now()->subDays(7),
            'end_date' => Carbon::now()->addDays(84),
            'status' => 'active',
            'location' => 'McKaynine Training Ground, Sandton',
        ]);
        $cgcClass->instructors()->attach($instructor2->id, ['is_lead' => true]);

        // Create class dates for EO class (weekly, 12 weeks)
        for ($week = 1; $week <= 12; $week++) {
            $date = ClassDate::create([
                'class_id' => $eoClass->id,
                'date' => Carbon::now()->subDays(14)->addWeeks($week - 1),
                'start_time' => '09:00',
                'end_time' => '10:00',
                'week_number' => $week,
                'is_off_week' => false,
            ]);
            // Add content for past weeks
            if ($week <= 2) {
                WeeklyContent::create([
                    'class_date_id' => $date->id,
                    'title' => "Week {$week} — " . ($week === 1 ? 'Introduction & Hand Target' : 'Focus & Heelwork Foundations'),
                    'description' => "This week we covered the fundamentals of " . ($week === 1 ? 'hand targeting and engagement' : 'focus and beginning heelwork') . ". Great work everyone!",
                    'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'practice_checklist' => json_encode($week === 1
                        ? ["Practice hand target 3x per day (30 reps)", "Work on name recognition", "Short engagement sessions (2-3 minutes)"]
                        : ["5 minutes heelwork practice daily", "Focus games: eye contact for 3 seconds", "Recall practice in garden"]),
                    'what_to_bring_next_week' => 'High-value treats, long line, treat pouch',
                    'is_published' => true,
                ]);
            }
        }

        // Enrolments
        $enrolment1 = Enrolment::create([
            'dog_id' => $dog1->id,
            'handler_id' => $handler1->id,
            'class_id' => $eoClass->id,
            'status' => 'confirmed',
            'pathway' => 'assessment',
            'enrolled_at' => Carbon::now()->subDays(21),
            'confirmed_at' => Carbon::now()->subDays(20),
        ]);

        $enrolment2 = Enrolment::create([
            'dog_id' => $dog2->id,
            'handler_id' => $handler2->id,
            'class_id' => $cgcClass->id,
            'status' => 'confirmed',
            'pathway' => 'assessment',
            'enrolled_at' => Carbon::now()->subDays(14),
            'confirmed_at' => Carbon::now()->subDays(13),
        ]);

        // Pending enrolment for Chloe's puppy
        Enrolment::create([
            'dog_id' => $dog3->id,
            'handler_id' => $handler3->id,
            'class_id' => $puppyClass->id,
            'status' => 'pending',
            'pathway' => 'puppy',
            'enrolled_at' => Carbon::now()->subDays(2),
        ]);

        // Assessment slot
        AssessmentSlot::create([
            'date' => Carbon::now()->addDays(5),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'max_bookings' => 3,
            'is_available' => true,
        ]);
        AssessmentSlot::create([
            'date' => Carbon::now()->addDays(12),
            'start_time' => '14:00',
            'end_time' => '15:00',
            'max_bookings' => 3,
            'is_available' => true,
        ]);

        // Resources
        Resource::create([
            'title' => 'Getting Started with Positive Training',
            'content' => "## Introduction to Positive Reinforcement\n\nPositive reinforcement training is based on rewarding behaviours you want to see more of. When your dog does something you like, you immediately reward them — and they'll want to repeat that behaviour.\n\n### Key Principles\n- **Timing matters**: Reward within 1-2 seconds of the desired behaviour\n- **Consistency is king**: Everyone in the household should use the same cues and rules\n- **Keep sessions short**: 2-5 minute sessions are more effective than 30-minute marathons",
            'category' => 'Training Tips',
            'is_published' => true,
            'sort_order' => 1,
            'created_by' => $admin->id,
        ]);
        Resource::create([
            'title' => 'What to Bring to Class',
            'content' => "## Your Class Checklist\n\n- **High-value treats** — small, smelly, irresistible. Cheese, chicken, liver treats work well\n- **Treat pouch or pocket** — you need easy access\n- **Your dog's regular lead** — 1.8m is ideal for class work\n- **Water bowl** — your dog may get thirsty\n- **Poo bags** — always!\n- **Patience and a sense of humour** — learning takes time",
            'category' => 'General',
            'is_published' => true,
            'sort_order' => 2,
            'created_by' => $admin->id,
        ]);
        Resource::create([
            'title' => 'Socialisation Guide for Puppies',
            'content' => "## Why Socialisation Matters\n\nThe critical socialisation window for puppies is between 3-14 weeks. During this time, positive exposure to a wide variety of experiences helps shape your puppy's confidence and temperament for life.",
            'category' => 'Socialisation',
            'is_published' => true,
            'sort_order' => 3,
            'created_by' => $admin->id,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@mckaynine.co.za / password');
        $this->command->info('Instructor: sarah@mckaynine.co.za / password');
        $this->command->info('Handler: emma@example.com / password');
        $this->command->info('Pending handler: chloe@example.com / password');
    }
}
