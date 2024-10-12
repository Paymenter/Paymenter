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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->foreignIdFor(\App\Models\Order::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Product::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->string('currency_code', 3);
            $table->integer('quantity')->default(1);
            $table->decimal('price', 17, 2);
            $table->foreignIdFor(\App\Models\Plan::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Coupon::class)->nullable()->constrained()->nullOnDelete();
            $table->dateTime('expires_at')->nullable();
            $table->string('subscription_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
