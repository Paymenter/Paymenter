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
        Schema::create('custom_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('key')->unique();
            $table->string('type');
            $table->string('model');
            $table->string('validation')->nullable();
            $table->json('allowed_values')->nullable();
            $table->boolean('non_editable');
            $table->boolean('required');
            $table->boolean('show_on_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_properties');
    }
};
