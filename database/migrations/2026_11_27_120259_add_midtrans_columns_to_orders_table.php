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
            $table->string('midtrans_order_id', 100)->nullable()->index()->after('payment_method');
            $table->string('snap_token')->nullable()->after('midtrans_order_id');
            $table->text('snap_redirect_url')->nullable()->after('snap_token');
            $table->string('midtrans_transaction_id', 100)->nullable()->after('snap_redirect_url');
            $table->string('midtrans_transaction_status', 50)->nullable()->after('midtrans_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['midtrans_order_id']);
            $table->dropColumn([
                'midtrans_order_id',
                'snap_token',
                'snap_redirect_url',
                'midtrans_transaction_id',
                'midtrans_transaction_status',
            ]);
        });
    }
};
