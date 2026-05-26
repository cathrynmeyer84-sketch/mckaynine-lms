<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('handlers', function (Blueprint $table) {
            $table->string('invoicesonline_client_id')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('handlers', function (Blueprint $table) {
            $table->dropColumn('invoicesonline_client_id');
        });
    }
};
