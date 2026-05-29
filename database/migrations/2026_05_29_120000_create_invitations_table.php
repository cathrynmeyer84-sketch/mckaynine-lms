<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name')->nullable();   // optional pre-fill
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
