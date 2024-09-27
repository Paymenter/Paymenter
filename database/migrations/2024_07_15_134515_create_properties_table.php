<?php

use App\Models\CustomProperty;
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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CustomProperty::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('key');
            $table->unique(['key', 'model_id', 'model_type']);
            $table->text('value');
            $table->morphs('model');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
