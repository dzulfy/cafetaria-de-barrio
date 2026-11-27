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
        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'guest_session_id')) {
                $table->uuid('guest_session_id')->nullable()->after('order_code');
                $table->foreign('guest_session_id')
                    ->references('id')
                    ->on('guest_sessions')
                    ->nullOnDelete();
                $table->index(['guest_session_id', 'created_at'], 'orders_guest_session_created_index');
            }

            if (! Schema::hasColumn('orders', 'table_id')) {
                $table->foreignId('table_id')
                    ->nullable()
                    ->after('guest_session_id')
                    ->constrained('cafe_tables')
                    ->nullOnDelete();
                $table->index('table_id', 'orders_table_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'guest_session_id')) {
                $table->dropForeign(['guest_session_id']);
                $table->dropIndex('orders_guest_session_created_index');
                $table->dropColumn('guest_session_id');
            }

            if (Schema::hasColumn('orders', 'table_id')) {
                $table->dropForeign(['table_id']);
                $table->dropIndex('orders_table_id_index');
                $table->dropColumn('table_id');
            }
        });
    }
};
