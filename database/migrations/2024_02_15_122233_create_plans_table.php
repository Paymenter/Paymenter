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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->morphs('priceable');
            $table->enum('type', ['free', 'one-time', 'recurring']);
            $table->integer('billing_period')->nullable();
            $table->enum('billing_unit', ['hour', 'day', 'week', 'month', 'year'])->nullable();
            $table->unsignedTinyInteger('sort')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
