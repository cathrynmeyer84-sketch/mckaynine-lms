<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained()->onDelete('cascade');
            $table->foreignId('graded_by')->constrained('users')->onDelete('cascade');
            $table->string('exam_type'); // cgc_bronze, elementary_obedience, etc.
            $table->decimal('total_score', 5, 2)->nullable();
            $table->string('achievement_level')->nullable(); // excellent_pass, pass, not_ready, merit, review, fail
            $table->boolean('has_blocking_fault')->default(false);
            $table->text('instructor_comments')->nullable();
            $table->string('evaluator_name')->nullable();
            $table->date('exam_date')->nullable();
            $table->enum('status', ['draft', 'submitted', 'admin_approved', 'released'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
