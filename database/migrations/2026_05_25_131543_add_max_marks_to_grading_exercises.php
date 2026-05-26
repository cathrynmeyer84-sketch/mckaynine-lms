<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('grading_exercises', function (Blueprint $table) {
            $table->decimal('max_marks', 8, 2)->nullable()->after('starting_marks');
        });

        // Seed max_marks = starting_marks for all existing exercises
        DB::table('grading_exercises')->whereNotNull('starting_marks')->update([
            'max_marks' => DB::raw('starting_marks'),
        ]);
    }

    public function down(): void
    {
        Schema::table('grading_exercises', function (Blueprint $table) {
            $table->dropColumn('max_marks');
        });
    }
};
