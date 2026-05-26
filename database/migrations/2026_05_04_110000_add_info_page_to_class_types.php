<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('tagline')->nullable()->after('slug');
            $table->text('about')->nullable()->after('tagline');
            $table->string('general_schedule')->nullable()->after('about');
            $table->decimal('cost_from', 8, 2)->nullable()->after('general_schedule');
            $table->text('cost_notes')->nullable()->after('cost_from');
            $table->string('image_path')->nullable()->after('cost_notes');
            $table->string('image_mobile_path')->nullable()->after('image_path');
            $table->string('promo_video_url')->nullable()->after('image_mobile_path');
            $table->json('gallery_images')->nullable()->after('promo_video_url');
            $table->text('testimonial_text')->nullable()->after('gallery_images');
            $table->string('testimonial_name')->nullable()->after('testimonial_text');
            $table->string('testimonial_photo_path')->nullable()->after('testimonial_name');
            $table->string('page_template')->default('default')->after('testimonial_photo_path');
            $table->boolean('info_page_enabled')->default(false)->after('page_template');
            $table->boolean('is_public')->default(false)->after('info_page_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn([
                'slug', 'tagline', 'about', 'general_schedule', 'cost_from', 'cost_notes',
                'image_path', 'image_mobile_path', 'promo_video_url', 'gallery_images',
                'testimonial_text', 'testimonial_name', 'testimonial_photo_path',
                'page_template', 'info_page_enabled', 'is_public',
            ]);
        });
    }
};
