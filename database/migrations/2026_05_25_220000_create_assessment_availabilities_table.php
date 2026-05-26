<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_availabilities', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('day_of_week'); // 0=Sunday … 6=Saturday
            $table->time('start_time');
            $table->unsignedSmallInteger('max_bookings')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_special_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->unsignedSmallInteger('max_bookings')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_special_dates');
        Schema::dropIfExists('assessment_availabilities');
    }
};
