<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('duration_type', ['term', 'ongoing'])->default('term');
            $table->unsignedInteger('term_weeks')->nullable(); // for term classes
            $table->enum('billing_period', ['monthly', 'yearly'])->nullable(); // for ongoing
            $table->boolean('has_structured_content')->default(false);
            $table->timestamps();
        });

        Schema::create('class_type_weeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_type_id')->constrained('class_types')->onDelete('cascade');
            $table->unsignedInteger('week_number');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('youtube_url')->nullable();
            $table->text('practice_checklist')->nullable();
            $table->text('what_to_bring_next_week')->nullable();
            $table->text('extra_notes')->nullable();
            $table->timestamps();

            $table->unique(['class_type_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_type_weeks');
        Schema::dropIfExists('class_types');
    }
};
