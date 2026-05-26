<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dogs', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('breed');
            $table->string('spay_neuter_status')->nullable()->after('gender'); // when_old_enough|already_done|not_planning
            $table->string('training_goal')->nullable()->after('training_ambition'); // competitive_dog_sport|chilled_canine_companion
            $table->boolean('has_health_issues')->nullable()->after('health_issues');
        });

        Schema::table('handlers', function (Blueprint $table) {
            $table->string('whatsapp_consent')->nullable()->after('whatsapp_permission'); // yes|no|unsure
            $table->string('photo_consent')->nullable()->after('social_media_permission');  // yes|no|unsure
            $table->json('hear_about_us_sources')->nullable()->after('hear_about_us');
        });

        Schema::table('enrolments', function (Blueprint $table) {
            $table->string('class_type_requested')->nullable()->after('pathway'); // puppy|elementary_obedience|cgc_bronze|other
            $table->string('branch')->nullable()->after('class_type_requested');  // honeydew|midstream|delta|randburg|foundation|mobility|randfontein
            $table->boolean('checklist_acknowledged')->default(false)->after('branch');
        });
    }

    public function down(): void
    {
        Schema::table('dogs', function (Blueprint $table) {
            $table->dropColumn(['gender', 'spay_neuter_status', 'training_goal', 'has_health_issues']);
        });

        Schema::table('handlers', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_consent', 'photo_consent', 'hear_about_us_sources']);
        });

        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropColumn(['class_type_requested', 'branch', 'checklist_acknowledged']);
        });
    }
};
