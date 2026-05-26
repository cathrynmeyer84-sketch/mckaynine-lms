<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->foreignId('assessment_request_id')->nullable()->after('class_id')->constrained('assessment_requests')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\AssessmentRequest::class);
        });
    }
};
