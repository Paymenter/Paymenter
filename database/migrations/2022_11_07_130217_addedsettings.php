<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('settings', 'home_page_text')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('home_page_text')->nullable()->default('Welcome to Paymenter');
            });
        }
        Schema::table('settings', function (Blueprint $table) {
            $table->string('advanced_mode')->default('false');
            $table->string('currency_position')->default('left');
            $table->string('app_name')->default('Paymenter');
            $table->boolean('sidebar')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['advanced_mode', 'currency_position', 'home_page_text', 'app_name', 'sidebar']);
        });
    }
};
