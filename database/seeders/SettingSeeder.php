<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            ['key' => 'maintenance', 'value' => 0],
            ['key' => 'theme', 'value' => 'default'],
            ['key' => 'recaptcha', 'value' => 0],
            ['key' => 'recaptcha_site_key', 'value' => null],
            ['key' => 'recaptcha_secret_key', 'value' => null],
            ['key' => 'seo_title', 'value' => 'Paymenter'],
            ['key' => 'seo_description', 'value' => 'Change this description in settings'],
            ['key' => 'seo_keywords', 'value' => null],
            ['key' => 'seo_twitter_card', 'value' => 1],
            ['key' => 'seo_image', 'value' => 'https://paymenter.org/assets/images/paymenter.png'],
            ['key' => 'currency_sign', 'value' => '$'],
            ['key' => 'home_page_text', 'value' => 'Welcome to Paymenter'],
            ['key' => 'advanced_mode', 'value' => false],
            ['key' => 'currency_position', 'value' => 'left'],
            ['key' => 'app_name', 'value' => 'Paymenter'],
            ['key' => 'sidebar', 'value' => 0],
            ['key' => 'currency', 'value' => 'USD'],
            ['key' => 'language', 'value' => 'en'],
            ['key' => 'snow', 'value' => 0],
            ['key' => 'allow_auto_log', 'value' => 0],
            ['key' => 'credits', 'value' => 0],
            ['key' => 'minimum_deposit', 'value' => '5'],
            ['key' => 'maximum_deposit', 'value' => '100'],
            ['key' => 'maximum_balance', 'value' => '300'],
            ['key' => 'timezone', 'value' => 'UTC'],
            ['key' => 'latest_version', 'value' => config('app.version')],
            ['key' => 'remove_unpaid_order_after', 'value' => 7],
            ['key' => 'run_cronjob_at', 'value' => '00:00']
        ];
        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
        }
        Setting::insertOrIgnore($settings);
    }
}
