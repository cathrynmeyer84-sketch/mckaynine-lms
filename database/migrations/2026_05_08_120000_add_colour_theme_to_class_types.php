<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->string('color_theme')->default('forest')->after('page_template');
            $table->string('hero_overlay_color', 7)->nullable()->after('color_theme');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn(['color_theme', 'hero_overlay_color']);
        });
    }
};
