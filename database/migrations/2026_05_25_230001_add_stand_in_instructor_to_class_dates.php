<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_dates', function (Blueprint $table) {
            $table->foreignId('stand_in_instructor_id')
                ->nullable()
                ->after('class_type_week_id')
                ->constrained('instructors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('class_dates', function (Blueprint $table) {
            $table->dropForeign(['stand_in_instructor_id']);
            $table->dropColumn('stand_in_instructor_id');
        });
    }
};
