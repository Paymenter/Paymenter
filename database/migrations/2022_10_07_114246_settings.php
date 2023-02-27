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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('maintenance')->default(false);
            $table->string('theme')->default('default');
            $table->boolean('recaptcha')->default(false);
            $table->string('recaptcha_site_key')->nullable();
            $table->string('recaptcha_secret_key')->nullable();
            $table->timestamps();
        });
        // Insert default settings
        \App\Models\Setting::create();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
