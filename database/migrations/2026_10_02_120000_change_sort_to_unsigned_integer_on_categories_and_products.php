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
        // Reordering a table writes positions 1..n, which outgrows a tiny integer
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('sort')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('sort')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedTinyInteger('sort')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedTinyInteger('sort')->nullable()->change();
        });
    }
};
