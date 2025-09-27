<?php

use App\Models\Cart;
use App\Models\Plan;
use App\Models\Product;
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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Cart::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Product::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Plan::class)->nullable()->constrained()->onDelete('cascade');
            $table->json('config_options')->nullable();
            $table->json('checkout_config')->nullable();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
