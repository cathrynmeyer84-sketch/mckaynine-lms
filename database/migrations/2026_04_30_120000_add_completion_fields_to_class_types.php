<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('class_types', function (Blueprint $table) {
            $table->string('enrolment_mode')->default('assessment')->after('rosette_image_path');
            $table->text('completion_message')->nullable()->after('enrolment_mode');
            $table->json('next_class_type_ids')->nullable()->after('completion_message');
        });
    }
    public function down(): void {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn(['enrolment_mode', 'completion_message', 'next_class_type_ids']);
        });
    }
};
