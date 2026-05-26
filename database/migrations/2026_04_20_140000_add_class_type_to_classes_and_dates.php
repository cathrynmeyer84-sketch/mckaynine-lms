<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('class_type_id')->nullable()->after('id')->constrained('class_types')->nullOnDelete();
        });

        Schema::table('class_dates', function (Blueprint $table) {
            $table->foreignId('class_type_week_id')->nullable()->after('week_number')->constrained('class_type_weeks')->nullOnDelete();
            $table->date('content_send_date')->nullable()->after('class_type_week_id');
            $table->timestamp('content_sent_at')->nullable()->after('content_send_date');
        });
    }

    public function down(): void
    {
        Schema::table('class_dates', function (Blueprint $table) {
            $table->dropForeign(['class_type_week_id']);
            $table->dropColumn(['class_type_week_id', 'content_send_date', 'content_sent_at']);
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['class_type_id']);
            $table->dropColumn('class_type_id');
        });
    }
};
