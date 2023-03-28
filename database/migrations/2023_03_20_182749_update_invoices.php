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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->unsignedBigInteger('order_id')->nullable()->change();
            $table->date('due_date')->nullable()->after('paid_at');
            $table->string('total')->nullable()->after('due_date');
        });	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->change();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->dropColumn('due_date');
            $table->dropColumn('total');
        });
    }
};
