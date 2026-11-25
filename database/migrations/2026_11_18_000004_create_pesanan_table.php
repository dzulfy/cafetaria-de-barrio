<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 20)->unique();
            $table->foreignId('meja_id')->nullable()->constrained('meja')->nullOnDelete();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->enum('sumber', ['qr', 'kasir']);
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->string('nama_pelanggan', 80)->nullable();
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->string('catatan')->nullable();
            $table->uuid('uuid_klien')->nullable()->unique();
            $table->dateTime('waktu_pesan');
            $table->timestamps();

            $table->index('status');
            $table->index('waktu_pesan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
