<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('coupon')->nullable();
        });
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('time')->default('lifetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('coupon');
        });
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('time');
        });
    }
};
