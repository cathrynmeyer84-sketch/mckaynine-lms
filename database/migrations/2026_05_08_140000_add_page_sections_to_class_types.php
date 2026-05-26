<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->text('trust_strap')->nullable()->after('hero_overlay_color');
            $table->text('helps_with')->nullable()->after('trust_strap');
            $table->text('age_requirements')->nullable()->after('helps_with');
            $table->text('what_to_bring')->nullable()->after('age_requirements');
            $table->text('how_to_join_steps')->nullable()->after('what_to_bring');
            $table->text('joining_notes')->nullable()->after('how_to_join_steps');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn(['trust_strap','helps_with','age_requirements','what_to_bring','how_to_join_steps','joining_notes']);
        });
    }
};
