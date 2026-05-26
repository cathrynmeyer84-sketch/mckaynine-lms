<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('end_date');
            $table->time('end_time')->nullable()->after('start_time');
        });

        Schema::table('class_dates', function (Blueprint $table) {
            $table->dropColumn('content_send_date');
        });

        Schema::table('class_dates', function (Blueprint $table) {
            $table->dateTime('content_send_date')->nullable()->after('class_type_week_id');
        });
    }

    public function down(): void
    {
        Schema::table('class_dates', function (Blueprint $table) {
            $table->dropColumn('content_send_date');
        });
        Schema::table('class_dates', function (Blueprint $table) {
            $table->date('content_send_date')->nullable()->after('class_type_week_id');
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
