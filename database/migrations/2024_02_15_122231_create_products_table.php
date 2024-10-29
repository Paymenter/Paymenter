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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('per_user_limit')->nullable();
            $table->unsignedTinyInteger('sort')->nullable();
            $table->enum('allow_quantity', ['disabled', 'separated', 'combined'])->default('disabled');
            $table->foreignIdFor(\App\Models\Server::class, 'server_id')->nullable();
            $table->text('email_template')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
