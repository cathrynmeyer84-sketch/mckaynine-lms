<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cgc_bronze_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_result_id')->constrained()->onDelete('cascade');
            // Rating: excellent, very_good, conditional, not_ready
            $table->string('test1_rating')->nullable();  $table->text('test1_notes')->nullable();
            $table->string('test2_rating')->nullable();  $table->text('test2_notes')->nullable();
            $table->string('test3a_rating')->nullable(); $table->text('test3a_notes')->nullable();
            $table->string('test3b_rating')->nullable(); $table->text('test3b_notes')->nullable();
            $table->string('test4_rating')->nullable();  $table->text('test4_notes')->nullable();
            $table->string('test5_rating')->nullable();  $table->text('test5_notes')->nullable();
            $table->string('test6_rating')->nullable();  $table->text('test6_notes')->nullable();
            $table->string('test7_rating')->nullable();  $table->text('test7_notes')->nullable();
            $table->string('test8_rating')->nullable();  $table->text('test8_notes')->nullable();
            $table->string('test9_rating')->nullable();  $table->text('test9_notes')->nullable();
            $table->string('test10_rating')->nullable(); $table->text('test10_notes')->nullable();
            $table->string('test11_rating')->nullable(); $table->text('test11_notes')->nullable();
            $table->string('test12_rating')->nullable(); $table->text('test12_notes')->nullable();
            $table->string('test13_rating')->nullable(); $table->text('test13_notes')->nullable();
            $table->decimal('calculated_score', 5, 2)->nullable();
            $table->boolean('has_blocking_fault')->default(false);
            $table->text('global_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cgc_bronze_grades');
    }
};
