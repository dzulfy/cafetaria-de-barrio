<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            if (! Schema::hasColumn('pengeluaran', 'kategori')) {
                $table->string('kategori', 50)->default('Operasional')->after('jumlah');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            if (Schema::hasColumn('pengeluaran', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
