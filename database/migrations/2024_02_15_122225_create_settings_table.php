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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->text('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->boolean('encrypted')->default(false);
            $table->nullableMorphs('settingable');
            $table->unique(['key', 'settingable_id', 'settingable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};