<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->json('next_class_ids')->nullable()->after('description');
        });
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn('next_class_type_ids');
        });
    }
    public function down(): void {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('next_class_ids');
        });
        Schema::table('class_types', function (Blueprint $table) {
            $table->json('next_class_type_ids')->nullable();
        });
    }
};
