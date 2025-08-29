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
            $table->dropForeign(['config_value_id']);
            $table->foreign('config_value_id')
                ->references('id')
                ->on('config_options')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_configs', function (Blueprint $table) {
            $table->dropForeign(['config_value_id']);
            $table->foreign('config_value_id')
                ->references('id')
                ->on('config_options');
        });
    }
};
