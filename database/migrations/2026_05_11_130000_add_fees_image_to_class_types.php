<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->string('fees_image_path')->nullable()->after('gallery_images');
            $table->string('fees_image_mobile_path')->nullable()->after('fees_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn(['fees_image_path', 'fees_image_mobile_path']);
        });
    }
};
