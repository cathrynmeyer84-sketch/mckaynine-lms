<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->boolean('info_page_enabled')->default(false)->after('description');
            $table->string('info_slug')->nullable()->unique()->after('info_page_enabled');
            $table->boolean('show_enrol_button')->default(true)->after('info_slug');
            $table->string('enrolment_form_type')->default('auto')->after('show_enrol_button'); // auto, puppy, assessment
            $table->decimal('course_price', 8, 2)->nullable()->after('enrolment_form_type');
            $table->decimal('enrolment_fee', 8, 2)->nullable()->after('course_price');
            $table->string('info_address')->nullable()->after('enrolment_fee');
            $table->string('bank_name')->nullable()->after('info_address');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_branch_code')->nullable()->after('bank_account_number');
            $table->string('bank_reference_note')->nullable()->after('bank_branch_code');
            $table->json('info_helps_with')->nullable()->after('bank_reference_note');
            $table->json('info_what_to_bring')->nullable()->after('info_helps_with');
            $table->text('info_age_requirements')->nullable()->after('info_what_to_bring');
            $table->text('info_joining_notes')->nullable()->after('info_age_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn([
                'info_page_enabled', 'info_slug', 'show_enrol_button', 'enrolment_form_type',
                'course_price', 'enrolment_fee', 'info_address',
                'bank_name', 'bank_account_name', 'bank_account_number', 'bank_branch_code', 'bank_reference_note',
                'info_helps_with', 'info_what_to_bring', 'info_age_requirements', 'info_joining_notes',
            ]);
        });
    }
};
