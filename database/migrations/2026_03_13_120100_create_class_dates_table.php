<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_off_week')->default(false);
            $table->string('off_week_reason')->nullable();
            $table->integer('week_number')->nullable(); // 1, 2, 3 etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_dates');
    }
};
