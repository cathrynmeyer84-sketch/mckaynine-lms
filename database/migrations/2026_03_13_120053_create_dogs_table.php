<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handler_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->string('breed');
            $table->string('gender_repro_status')->nullable(); // female_intact, female_spayed, male_intact, male_neutered
            $table->string('origin_story')->nullable();
            $table->string('age_when_acquired')->nullable();
            $table->json('animal_buddies_at_home')->nullable();
            $table->json('young_children_at_home')->nullable();
            $table->string('socialisation_other_dogs')->nullable();
            $table->string('socialisation_other_animals')->nullable();
            $table->string('socialisation_people')->nullable();
            $table->string('socialisation_sights_sounds')->nullable();
            $table->text('socialisation_details')->nullable();
            $table->integer('training_ambition')->nullable(); // 1-5
            $table->boolean('has_behaviour_problems')->nullable();
            $table->text('behaviour_problems_details')->nullable();
            $table->text('health_issues')->nullable();
            $table->string('photo_path', 2048)->nullable();
            $table->string('vaccination_card_path', 2048)->nullable();
            $table->date('vaccination_expiry_date')->nullable();
            $table->boolean('is_retired')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dogs');
    }
};
