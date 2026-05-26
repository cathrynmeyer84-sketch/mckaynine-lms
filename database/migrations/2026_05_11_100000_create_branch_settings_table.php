<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('branch_settings', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->decimal('enrolment_fee', 8, 2)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch_code')->nullable();
            $table->string('bank_reference_note')->nullable();
            $table->string('legal_entity_name')->nullable();
            $table->string('legal_registration_number')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('branch_settings'); }
};
