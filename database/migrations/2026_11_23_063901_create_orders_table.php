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
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_code')->unique();
                $table->string('table_number')->default('05');
                $table->string('customer_name')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedInteger('subtotal')->default(0);
                $table->unsignedInteger('total_price')->default(0);
                $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
                $table->string('payment_status')->default('unpaid');
                $table->timestamps();
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                if (! Schema::hasColumn('orders', 'notes')) {
                    $table->text('notes')->nullable()->after('customer_name');
                }
                if (! Schema::hasColumn('orders', 'subtotal')) {
                    $table->unsignedInteger('subtotal')->default(0)->after('notes');
                }
                if (! Schema::hasColumn('orders', 'total_price')) {
                    $table->unsignedInteger('total_price')->default(0)->after('subtotal');
                }
                if (! Schema::hasColumn('orders', 'payment_status')) {
                    $table->string('payment_status')->default('unpaid')->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
