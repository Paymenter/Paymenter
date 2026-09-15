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
        Schema::table('config_options', function (Blueprint $table) {
            $table->decimal('min', 20, 2)->nullable()->after('upgradable');
            $table->decimal('max', 20, 2)->nullable()->after('min');
            $table->decimal('step', 20, 2)->nullable()->after('max');
            $table->boolean('show_as_slider')->default(false)->after('step');
        });

        Schema::table('service_configs', function (Blueprint $table) {
            $table->string('value')->nullable()->after('config_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('config_options', function (Blueprint $table) {
            $table->dropColumn(['min', 'max', 'step', 'show_as_slider']);
        });

        Schema::table('service_configs', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
};
