<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', [
                'puppy_class', 'elementary_obedience_3m', 'elementary_obedience_2m',
                'positive_foundations', 'cgc_bronze', 'cgc_silver', 'cgc_gold',
                'rally_beginner', 'rally_novice', 'rally_advanced',
                'tracking', 'working_trials', 'foundation_agility', 'agility',
                'beginner_hoopers', 'advanced_hoopers', 'k9_yoga', 'brain_and_body',
                'man_work', 'scent_work'
            ]);
            $table->boolean('has_final_exam')->default(false);
            $table->integer('max_capacity')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });

        // Pivot: class_instructor
        Schema::create('class_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->boolean('is_lead')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_instructor');
        Schema::dropIfExists('classes');
    }
};
