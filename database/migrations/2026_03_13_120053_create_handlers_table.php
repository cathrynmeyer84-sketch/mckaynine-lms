<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handlers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('cell_number');
            $table->string('account_holder_name')->nullable();
            $table->string('occupation')->nullable();
            $table->string('vet_name_location');
            $table->boolean('whatsapp_permission')->default(false);
            $table->boolean('social_media_permission')->default(false);
            $table->boolean('ground_rules_agreed')->default(false);
            $table->boolean('terms_agreed')->default(false);
            $table->text('hear_about_us')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handlers');
    }
};
