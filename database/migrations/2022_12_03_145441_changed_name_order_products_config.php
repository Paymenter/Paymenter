<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products_config', function (Blueprint $table) {
            $table->renameColumn('order_id', 'order_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products_config', function (Blueprint $table) {
            $table->renameColumn('order_product_id', 'order_id');
        });
    }
};
