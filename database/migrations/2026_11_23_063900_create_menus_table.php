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
        if (! Schema::hasTable('menus')) {
            Schema::create('menus', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('price');
                $table->string('category')->default('minuman');
                $table->string('icon')->default('coffee');
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('menus', function (Blueprint $table) {
                if (! Schema::hasColumn('menus', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                if (! Schema::hasColumn('menus', 'category')) {
                    $table->string('category')->default('minuman')->after('price');
                }
                if (! Schema::hasColumn('menus', 'icon')) {
                    $table->string('icon')->default('coffee')->after('category');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
