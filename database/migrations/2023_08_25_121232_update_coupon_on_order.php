<?php

use App\Models\Coupon;
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
        $orders = \App\Models\Order::all();
        foreach ($orders as $order) {
            if (!Coupon::find($order->coupon)) {
                $order->coupon = null;
                $order->save();
            }
        }
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('coupon');
            $table->foreignIdFor(Coupon::class, 'coupon_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        foreach ($orders as $order) {
            $order->coupon_id = $order->coupon;
            unset($order->coupon);
            $order->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor(Coupon::class, 'coupon_id');
            $table->string('coupon')->nullable()->after('user_id');
        });
    }
};
