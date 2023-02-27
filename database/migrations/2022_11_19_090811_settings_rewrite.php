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
        Schema::table('settings', function (Blueprint $table) {
            // add value + key
            $table->string('key')->nullable();
            $table->string('value')->nullable();
        });
        // migrate old settings
        $settings = \App\Models\Setting::first();
        $settings = $settings->toArray();
        foreach ($settings as $key => $value) {
            if ($key == 'created_at' || $key == 'updated_at' || $key == 'id' || $key == 'key' || $key == 'value') {
                continue;
            }
            \App\Models\Setting::create([
                'key' => $key,
                'value' => $value,
            ]);
        }
        \App\Models\Setting::first()->delete();
        // drop old settings
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['advanced_mode', 'currency_sign', 'currency_position', 'home_page_text', 'app_name', 'sidebar', 'seo_title', 'seo_description', 'seo_keywords', 'seo_twitter_card', 'seo_image', 'maintenance', 'theme', 'recaptcha', 'recaptcha_site_key', 'recaptcha_secret_key']);
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
            $table->boolean('advanced_mode')->default(false);
            $table->string('currency_sign')->default('$');
            $table->string('currency_position')->default('left');
            $table->text('home_page_text')->nullable();
            $table->string('app_name')->default('App');
            $table->string('sidebar')->default('dark');
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->string('seo_twitter_card')->nullable();
            $table->string('seo_image')->nullable();
            $table->boolean('maintenance')->default(false);
            $table->string('theme')->default('default');
            $table->boolean('recaptcha')->default(false);
            $table->string('recaptcha_site_key')->nullable();
            $table->string('recaptcha_secret_key')->nullable();
        });
        // migrate old settings
        $settings = \App\Models\Setting::all();
        $settings = $settings->toArray();
        foreach ($settings as $key => $value) {
            \App\Models\Setting::create([
                $value['key'] => $value['value'],
            ]);
        }
        // drop old settings
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['key', 'value']);
        });
    }
};
