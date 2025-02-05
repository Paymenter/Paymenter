<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ext_affiliate_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Affiliate::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->unique()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_orders');
    }
};
