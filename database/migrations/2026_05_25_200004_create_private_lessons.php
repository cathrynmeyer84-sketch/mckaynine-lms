<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handler_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dog_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instructor_id')->constrained()->cascadeOnDelete();
            $table->date('requested_date');
            $table->time('requested_start_time');
            $table->date('confirmed_date')->nullable();
            $table->time('confirmed_start_time')->nullable();
            $table->string('status')->default('pending');
            $table->text('handler_notes')->nullable();
            $table->text('instructor_notes')->nullable();
            $table->text('reschedule_note')->nullable();
            $table->decimal('fee', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_lessons');
    }
};
