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
            if (! Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('order_code')
                    ->constrained('pengguna')
                    ->nullOnDelete();
                $table->index(['user_id', 'created_at'], 'orders_user_created_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropIndex('orders_user_created_index');
                $table->dropColumn('user_id');
            }
        });
    }
};
