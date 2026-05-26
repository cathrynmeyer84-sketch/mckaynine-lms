<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_fee_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained()->cascadeOnDelete();
            $table->string('term');            // e.g. '2026-T2'
            $table->string('period_label');    // e.g. 'Term 2 2026 (Apr – Jun)'
            $table->date('period_start');
            $table->date('period_end');
            $table->json('lines');             // serialised fee lines (plain arrays)
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_released')->default(false);
            $table->timestamp('released_at')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamps();
            $table->unique(['instructor_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_fee_statements');
    }
};
