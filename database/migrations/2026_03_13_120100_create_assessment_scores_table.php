<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            // 7 step scores (1-5 each)
            $table->integer('step1_score')->nullable(); // Entry Observation
            $table->integer('step2_score')->nullable(); // Comfort Level
            $table->integer('step3_score')->nullable(); // Simple Cue Check
            $table->integer('step4_score')->nullable(); // Neutral Stranger Approach
            $table->integer('step5_score')->nullable(); // Walking Past Dogs
            $table->integer('step6_score')->nullable(); // Low-Level Distraction
            $table->integer('step7_score')->nullable(); // Handler Separation
            $table->boolean('step7_skipped')->default(false);
            $table->string('step7_skip_reason')->nullable();
            $table->string('recommended_outcome')->nullable(); // group_class, private_lessons, behaviourist
            $table->string('final_outcome')->nullable(); // after evaluator confirms/overrides
            $table->text('override_reason')->nullable();
            $table->text('staff_notes')->nullable();
            $table->text('staff_notes2')->nullable();
            $table->text('global_notes')->nullable();
            $table->enum('status', ['draft', 'submitted', 'admin_reviewed', 'outcome_sent'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
    }
};
