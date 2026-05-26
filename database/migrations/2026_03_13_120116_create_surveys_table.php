<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained()->onDelete('cascade');
            $table->foreignId('handler_id')->constrained()->onDelete('cascade');
            $table->integer('overall_rating')->nullable(); // 1-5
            $table->integer('instructor_rating')->nullable(); // 1-5
            $table->text('most_valuable')->nullable();
            $table->text('suggestions')->nullable();
            $table->integer('likelihood_to_recommend')->nullable(); // 1-10 NPS
            $table->text('comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
