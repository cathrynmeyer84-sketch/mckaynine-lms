<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_requests', function (Blueprint $table) {
            $table->string('gender_repro_status')->nullable()->after('how_long_had_dog');
            $table->string('where_got_dog')->nullable()->after('gender_repro_status');
            $table->string('age_acquired')->nullable()->after('where_got_dog');
            $table->json('aggression_targets')->nullable()->after('aggression_history');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_requests', function (Blueprint $table) {
            $table->dropColumn(['gender_repro_status', 'where_got_dog', 'age_acquired', 'aggression_targets']);
        });
    }
};
