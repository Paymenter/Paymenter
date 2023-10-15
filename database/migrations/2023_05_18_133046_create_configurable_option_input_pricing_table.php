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
        Schema::create('configurable_option_input_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('input_id');
            $table->foreign('input_id')->references('id')->on('configurable_option_inputs')->onDelete('cascade');
            $table->decimal('monthly', 10, 2)->nullable();
            $table->decimal('quarterly', 10, 2)->nullable();
            $table->decimal('semi_annually', 10, 2)->nullable();
            $table->decimal('annually', 10, 2)->nullable();
            $table->decimal('biennially', 10, 2)->nullable();
            $table->decimal('triennially', 10, 2)->nullable();
            $table->decimal('monthly_setup', 10, 2)->nullable();
            $table->decimal('quarterly_setup', 10, 2)->nullable();
            $table->decimal('semi_annually_setup', 10, 2)->nullable();
            $table->decimal('annually_setup', 10, 2)->nullable();
            $table->decimal('biennially_setup', 10, 2)->nullable();
            $table->decimal('triennially_setup', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurable_option_input_pricing');
    }
};
