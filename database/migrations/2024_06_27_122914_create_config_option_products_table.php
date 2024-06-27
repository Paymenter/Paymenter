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
        Schema::create('config_option_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\ConfigOption::class, 'config_option_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Product::class, 'product_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_option_products');
    }
};
