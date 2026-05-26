<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_date_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('youtube_url')->nullable();
            $table->text('practice_checklist')->nullable(); // JSON or markdown
            $table->text('what_to_bring_next_week')->nullable();
            $table->text('extra_notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('publish_at')->nullable(); // scheduled publish
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_contents');
    }
};
