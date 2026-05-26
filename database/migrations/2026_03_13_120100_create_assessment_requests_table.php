<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dog_id')->constrained()->onDelete('cascade');
            $table->foreignId('handler_id')->constrained()->onDelete('cascade');
            // Assessment intake form fields
            $table->string('dog_age_description')->nullable(); // "8 months" free text
            $table->string('how_long_had_dog')->nullable();
            $table->text('health_concerns')->nullable();
            $table->json('training_goals')->nullable();
            $table->text('desired_outcomes')->nullable();
            $table->text('specific_issues')->nullable();
            $table->integer('response_to_new_people')->nullable(); // 1-5
            $table->string('behaviour_around_dogs')->nullable();
            $table->string('aggression_history')->nullable();
            $table->text('aggression_details')->nullable();
            $table->text('prior_training')->nullable();
            $table->string('comfort_in_busy_environments')->nullable();
            $table->text('comfortable_with_assessment')->nullable();
            $table->string('open_to_recommendation')->nullable();
            $table->text('additional_notes')->nullable();
            $table->boolean('terms_agreed')->default(false);
            $table->boolean('requirements_acknowledged')->default(false);
            $table->enum('status', ['pending', 'reviewed', 'slot_offered', 'booked', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assessment_slot_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_requests');
    }
};
