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

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key');
            $table->enum('type', ['select', 'slider', 'radio', 'checkbox', 'number', 'text']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
