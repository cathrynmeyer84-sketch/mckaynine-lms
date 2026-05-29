<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->json('documents')->nullable()->after('gallery_images');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn('documents');
        });
    }
};
