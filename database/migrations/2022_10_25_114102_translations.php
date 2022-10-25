<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add trnaslations field to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('translations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // remove translations field from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('translations');
        });
    }
};
