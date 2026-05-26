<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->foreignId('assigned_instructor_id')->nullable()->after('assessment_request_id')
                  ->constrained('instructors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Instructor::class, 'assigned_instructor_id');
            $table->dropColumn('assigned_instructor_id');
        });
    }
};
