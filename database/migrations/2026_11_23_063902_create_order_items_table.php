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
        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('menu_id')->nullable()->constrained()->nullOnDelete();
                $table->string('menu_name');
                $table->unsignedInteger('price');
                $table->unsignedInteger('quantity');
                $table->unsignedInteger('subtotal');
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('order_items', 'menu_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('menu_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
