<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('email');
            $table->boolean('is_instructor')->default(false)->after('is_admin');
            $table->boolean('is_handler')->default(true)->after('is_instructor');
            $table->string('profile_photo_path', 2048)->nullable()->after('is_handler');
            $table->timestamp('access_expires_at')->nullable()->after('profile_photo_path');
            $table->boolean('is_active')->default(true)->after('access_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'is_instructor', 'is_handler', 'profile_photo_path', 'access_expires_at', 'is_active']);
        });
    }
};
