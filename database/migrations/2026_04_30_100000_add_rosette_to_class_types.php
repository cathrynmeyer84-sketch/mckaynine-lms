<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('class_types', function (Blueprint $table) {
            $table->string('rosette_image_path')->nullable()->after('has_grading');
        });
    }
    public function down(): void {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn('rosette_image_path');
        });
    }
};
