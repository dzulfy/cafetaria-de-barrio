<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_menu', function (Blueprint $table) {
            $table->foreignId('menu_id')->constrained('menu')->cascadeOnDelete();
            $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
            $table->decimal('jumlah_pakai', 10, 2);
            $table->primary(['menu_id', 'bahan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_menu');
    }
};
