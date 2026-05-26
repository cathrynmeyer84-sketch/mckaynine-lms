<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('instructor_briefing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_type_week_id')->constrained('class_type_weeks')->cascadeOnDelete();
            $table->string('exercise_name');
            $table->text('description')->nullable();
            $table->string('suggested_time')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('instructor_briefing_items');
    }
};
