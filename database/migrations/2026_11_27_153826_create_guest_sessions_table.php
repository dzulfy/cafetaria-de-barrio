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
        if (! Schema::hasTable('guest_sessions')) {
            Schema::create('guest_sessions', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->foreignId('table_id')->nullable()->constrained('cafe_tables')->nullOnDelete();
                $table->timestamp('last_activity_at')->nullable()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_sessions');
    }
};
