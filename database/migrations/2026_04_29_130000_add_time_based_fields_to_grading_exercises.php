<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grading_exercises', function (Blueprint $table) {
            $table->unsignedInteger('target_time_seconds')->nullable()->after('starting_marks');
            $table->boolean('allow_second_attempt')->default(false)->after('target_time_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('grading_exercises', function (Blueprint $table) {
            $table->dropColumn(['target_time_seconds', 'allow_second_attempt']);
        });
    }
};
