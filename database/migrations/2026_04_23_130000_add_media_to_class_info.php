<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->string('info_hero_image_path', 2048)->nullable()->after('info_joining_notes');
            $table->text('testimonial_text')->nullable()->after('info_hero_image_path');
            $table->string('testimonial_name')->nullable()->after('testimonial_text');
            $table->string('testimonial_photo_path', 2048)->nullable()->after('testimonial_name');
            $table->string('info_tagline')->nullable()->after('testimonial_photo_path');
            $table->string('contact_phone')->nullable()->after('info_tagline');
            $table->string('contact_email')->nullable()->after('contact_phone');
        });
    }
    public function down(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['info_hero_image_path','testimonial_text','testimonial_name','testimonial_photo_path','info_tagline','contact_phone','contact_email']);
        });
    }
};
