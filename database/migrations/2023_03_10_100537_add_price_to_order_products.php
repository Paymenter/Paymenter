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
        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->after('quantity')->nullable();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total', 10, 2)->after('status');
        });
    }
};
