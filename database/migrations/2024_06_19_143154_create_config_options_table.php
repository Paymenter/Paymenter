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
        Schema::create('config_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('env_variable')->nullable();
            $table->string('type')->nullable();
            $table->unsignedTinyInteger('sort')->nullable();
            $table->boolean('hidden')->default(false);
            $table->timestamps();
        });
        Schema::table('config_options', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\ConfigOption::class, 'parent_id')->nullable()->constrained('config_options')->after('hidden')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_options');
    }
};
