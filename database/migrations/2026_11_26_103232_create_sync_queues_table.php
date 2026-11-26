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
        Schema::create('sync_queues', function (Blueprint $table) {
            $table->id();
            $table->string('local_id')->unique(); // e.g. a41fc9e2, or full uuid
            $table->string('short_id')->nullable(); // e.g. a41f...c9e2
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->json('order_payload')->nullable();
            $table->unsignedInteger('total_amount')->default(0);
            $table->string('description')->default('Menunggu koneksi');
            $table->string('status')->default('pending'); // pending, failed, synced
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_queues');
    }
};
