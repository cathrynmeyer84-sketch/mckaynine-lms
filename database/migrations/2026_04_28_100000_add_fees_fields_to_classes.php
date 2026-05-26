<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->text('course_fee_notes')->nullable()->after('enrolment_fee');
            $table->string('fees_image_path')->nullable()->after('course_fee_notes');
        });
    }

    public function down(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['course_fee_notes', 'fees_image_path']);
        });
    }
};
