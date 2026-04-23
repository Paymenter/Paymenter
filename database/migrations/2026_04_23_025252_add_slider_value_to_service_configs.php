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
        Schema::table('service_configs', function (Blueprint $table) {
            // Make config_value_id nullable so dynamic_slider rows can omit it
            $table->foreignId('config_value_id')->nullable()->change();
            // Numeric value for dynamic_slider config options
            $table->decimal('slider_value', 12, 4)->nullable()->after('config_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_configs', function (Blueprint $table) {
            $table->dropColumn('slider_value');
        });

        // Remove dynamic_slider rows (config_value_id = NULL) before restoring NOT NULL constraint.
        // These rows cannot be preserved without a valid config_value_id FK.
        \Illuminate\Support\Facades\DB::table('service_configs')->whereNull('config_value_id')->delete();

        Schema::table('service_configs', function (Blueprint $table) {
            $table->foreignId('config_value_id')->nullable(false)->change();
        });
    }
};
