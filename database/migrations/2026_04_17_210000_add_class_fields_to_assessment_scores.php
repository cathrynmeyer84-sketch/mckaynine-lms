<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_scores', function (Blueprint $table) {
            $table->string('recommended_class_name')->nullable()->after('global_notes');
            $table->string('recommended_class_url')->nullable()->after('recommended_class_name');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_scores', function (Blueprint $table) {
            $table->dropColumn(['recommended_class_name', 'recommended_class_url']);
        });
    }
};
