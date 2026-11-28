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
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('meja');
        Schema::dropIfExists('restaurant_tables');

        if (Schema::hasTable('resep_menu')) {
            Schema::drop('resep_menu');
            Schema::create('resep_menu', function (Blueprint $table): void {
                $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
                $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
                $table->decimal('jumlah_pakai', 10, 2);
                $table->primary(['menu_id', 'bahan_id']);
            });
        }

        Schema::dropIfExists('menu');
        Schema::dropIfExists('kategori');

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'menu_item_id')) {
            Schema::table('order_items', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('menu_item_id');
            });
        }

        Schema::dropIfExists('menu_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way cleanup of duplicate legacy tables; canonical schema remains intact.
    }
};
