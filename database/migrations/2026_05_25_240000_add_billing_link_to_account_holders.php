<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_holders', function (Blueprint $table) {
            $table->foreignId('linked_handler_id')
                ->nullable()
                ->after('handler_id')
                ->constrained('handlers')
                ->nullOnDelete();

            $table->string('link_status', 20)
                ->nullable()
                ->after('invoicing_notes')
                ->comment('null=external, pending_approval, approved, rejected');

            $table->string('link_token', 64)
                ->nullable()
                ->unique()
                ->after('link_status');

            $table->timestamp('link_expires_at')
                ->nullable()
                ->after('link_token');
        });
    }

    public function down(): void
    {
        Schema::table('account_holders', function (Blueprint $table) {
            $table->dropForeign(['linked_handler_id']);
            $table->dropColumn(['linked_handler_id', 'link_status', 'link_token', 'link_expires_at']);
        });
    }
};
