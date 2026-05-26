<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_pops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handler_id')->constrained()->onDelete('cascade');
            $table->string('invoice_reference')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('file_path');
            $table->boolean('is_reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_pops');
    }
};
