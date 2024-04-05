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
        Schema::create('order_product_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\OrderProduct::class, 'order_product_id');
            $table->foreignIdFor(App\Models\Invoice::class, 'invoice_id');
            $table->foreignIdFor(App\Models\Product::class, 'product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_product_upgrades');
    }
};
