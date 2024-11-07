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
        Schema::create('ext_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ext_announcements');
    }
};
