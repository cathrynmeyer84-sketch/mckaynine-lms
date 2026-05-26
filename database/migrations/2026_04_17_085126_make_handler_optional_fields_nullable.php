<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('handlers', function (Blueprint $table) {
            $table->string('vet_name_location')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('handlers', function (Blueprint $table) {
            $table->string('vet_name_location')->nullable(false)->change();
        });
    }
};
