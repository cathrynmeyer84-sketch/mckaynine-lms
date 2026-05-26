<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('info_hero_image_mobile_path')->nullable()->after('info_hero_image_path');
            $table->string('fees_image_mobile_path')->nullable()->after('fees_image_path');
        });
    }
    public function down(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['info_hero_image_mobile_path', 'fees_image_mobile_path']);
        });
    }
};
