<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_settings', function (Blueprint $table) {
            $table->string('io_username')->nullable()->after('private_lesson_fee');
            $table->string('io_password')->nullable()->after('io_username');
            $table->string('io_business_id')->nullable()->after('io_password');
            $table->string('invoicesonline_client_id')->nullable()->after('io_business_id')
                ->comment('This branch\'s own IO client ID');
        });
    }

    public function down(): void
    {
        Schema::table('branch_settings', function (Blueprint $table) {
            $table->dropColumn(['io_username', 'io_password', 'io_business_id', 'invoicesonline_client_id']);
        });
    }
};
