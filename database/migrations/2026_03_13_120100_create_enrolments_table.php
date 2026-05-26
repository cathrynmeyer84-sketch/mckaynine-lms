<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dog_id')->constrained()->onDelete('cascade');
            $table->foreignId('handler_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->enum('status', ['pending', 'waitlisted', 'confirmed', 'completed', 'withdrawn'])->default('pending');
            $table->enum('pathway', ['puppy', 'assessment'])->default('puppy');
            $table->date('enrolled_at')->nullable();
            $table->date('confirmed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('invoice_sent')->default(false);
            $table->string('invoice_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrolments');
    }
};
