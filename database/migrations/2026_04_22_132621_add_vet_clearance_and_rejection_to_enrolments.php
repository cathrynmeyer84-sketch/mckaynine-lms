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
        Schema::table('enrolments', function (Blueprint $table) {
            $table->string('vet_clearance_path')->nullable()->after('checklist_acknowledged');
            $table->timestamp('vet_clearance_requested_at')->nullable()->after('vet_clearance_path');
            $table->text('rejection_reason')->nullable()->after('vet_clearance_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropColumn(['vet_clearance_path', 'vet_clearance_requested_at', 'rejection_reason']);
        });
    }
};
