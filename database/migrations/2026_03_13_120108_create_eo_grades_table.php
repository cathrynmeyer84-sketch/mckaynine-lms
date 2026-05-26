<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eo_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->constrained()->onDelete('cascade');
            $table->enum('course_level', ['EO2', 'EO3']);
            // Exercise penalties (stored as JSON arrays of deductions)
            $table->json('ex1_penalties')->nullable(); // hand target
            $table->json('ex2_penalties')->nullable(); // focus
            $table->json('ex3_penalties')->nullable(); // heelwork
            $table->json('ex4_penalties')->nullable(); // recall
            $table->json('ex5_penalties')->nullable(); // sit & downs
            $table->json('ex6_penalties')->nullable(); // stays
            $table->json('ex7_penalties')->nullable(); // examination
            // Calculated scores per exercise
            $table->decimal('ex1_score', 5, 2)->nullable();
            $table->decimal('ex2_score', 5, 2)->nullable();
            $table->decimal('ex3_score', 5, 2)->nullable();
            $table->decimal('ex4_score', 5, 2)->nullable();
            $table->decimal('ex5_score', 5, 2)->nullable();
            $table->decimal('ex6_score', 5, 2)->nullable();
            $table->decimal('ex7_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2)->nullable();
            $table->text('global_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_grades');
    }
};
