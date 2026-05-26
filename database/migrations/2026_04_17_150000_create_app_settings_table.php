<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label');
            $table->timestamps();
        });

        DB::table('app_settings')->insert([
            ['key' => 'assessment_location',     'label' => 'Assessment Location',         'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'assessment_instructions', 'label' => 'On-the-day Instructions',     'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_email',             'label' => 'Admin Notification Email',    'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
