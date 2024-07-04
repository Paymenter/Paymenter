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
        Schema::create('currencies', function (Blueprint $table) {
            $table->string('code', 3)->primary();
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->enum('format', ['1.000,00', '1,000.00', '1 000,00', '1 000.00'])->default('1.000,00');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
