<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_type_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['marks', 'rating']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('starting_marks', 6, 2)->nullable(); // max marks for marks-based
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('grading_deduction_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_exercise_id')->constrained()->onDelete('cascade');
            $table->string('event_name');
            $table->decimal('marks_deducted', 6, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('grading_rating_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_exercise_id')->constrained()->onDelete('cascade');
            $table->string('label'); // e.g. "Excellent", "Good", "Fail"
            $table->decimal('marks_deducted', 6, 2)->default(0);
            $table->boolean('is_automatic_fail')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_rating_scales');
        Schema::dropIfExists('grading_deduction_events');
        Schema::dropIfExists('grading_exercises');
    }
};
