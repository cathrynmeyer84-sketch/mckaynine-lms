<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_weeks', function (Blueprint $table) {
            $table->id();
            $table->date('week_start');   // always the Monday of that week
            $table->boolean('is_active')->default(true);
            $table->string('label')->nullable();   // e.g. "Easter break", "Public holiday"
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();

            $table->unique('week_start');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_weeks');
    }
};
