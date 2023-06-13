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
        Schema::table('order_products_config', function (Blueprint $table) {
            $table->boolean('is_configurable_option')->default(false)->after('order_product_id');
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
            $table->dropColumn('is_configurable_option');
        });
    }
};
