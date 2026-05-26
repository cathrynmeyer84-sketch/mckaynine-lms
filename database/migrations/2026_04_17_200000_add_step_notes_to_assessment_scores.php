<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_scores', function (Blueprint $table) {
            $table->text('step1_notes')->nullable()->after('step1_score');
            $table->text('step2_notes')->nullable()->after('step2_score');
            $table->text('step3_notes')->nullable()->after('step3_score');
            $table->text('step4_notes')->nullable()->after('step4_score');
            $table->text('step5_notes')->nullable()->after('step5_score');
            $table->text('step6_notes')->nullable()->after('step6_score');
            $table->text('step7_notes')->nullable()->after('step7_score');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_scores', function (Blueprint $table) {
            $table->dropColumn(['step1_notes','step2_notes','step3_notes','step4_notes','step5_notes','step6_notes','step7_notes']);
        });
    }
};
