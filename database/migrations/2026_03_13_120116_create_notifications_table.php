<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Custom in-app notifications log (separate from Laravel's built-in notifications)
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // enrolment_confirmed, content_released, result_released, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // extra context
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('channel'); // email, in_app
            $table->string('type'); // notification type
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->enum('status', ['sent', 'failed', 'pending'])->default('sent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('app_notifications');
    }
};
