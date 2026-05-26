<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->boolean('private_lessons_enabled')->default(false)->after('is_active');
            $table->text('private_lesson_bio')->nullable()->after('private_lessons_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn(['private_lessons_enabled', 'private_lesson_bio']);
        });
    }
};
