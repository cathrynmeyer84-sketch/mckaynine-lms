<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->boolean('is_entry_class')->default(false)->after('page_template');
        });
    }

    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn('is_entry_class');
        });
    }
};
