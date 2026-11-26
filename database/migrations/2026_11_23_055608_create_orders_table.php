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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('table_number');
            $table->string('customer_name')->nullable();
            $table->string('source')->default('QR Meja');
            $table->string('status')->default('menunggu'); // menunggu, diproses, selesai, dibatalkan
            $table->text('customer_notes')->nullable();
            $table->unsignedInteger('total_amount')->default(0);
            $table->string('payment_method')->nullable(); // tunai, qris, transfer
            $table->unsignedInteger('amount_paid')->nullable();
            $table->unsignedInteger('change_amount')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
