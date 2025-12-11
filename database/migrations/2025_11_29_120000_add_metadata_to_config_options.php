<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds metadata JSON column to config_options table for storing
     * dynamic_slider configuration (min, max, step, pricing, etc.)
     */
    public function up(): void
    {
        Schema::table('config_options', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('upgradable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_options', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
