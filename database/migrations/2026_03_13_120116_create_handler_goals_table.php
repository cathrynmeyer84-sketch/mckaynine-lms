<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handler_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained()->onDelete('cascade');
            $table->text('goal');
            $table->text('progress_notes')->nullable();
            $table->boolean('visible_to_handler')->default(false);
            $table->enum('status', ['active', 'achieved', 'dropped'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handler_goals');
    }
};
