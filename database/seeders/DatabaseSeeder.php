<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Role::updateOrCreate(['name' => 'user'], ['permissions' => []]);
        Role::updateOrCreate(['name' => 'admin'], ['permissions' => ['*']]);

        foreach (\App\Classes\Settings::settings() as $settings) {
            foreach ($settings as $setting) {
                if (!isset($setting['default'])) {
                    continue;
                }
                Setting::firstOrCreate([
                    'key' => $setting['name'],
                ], [
                    'value' => $setting['default'],
                    'type' => $setting['database_type'] ?? 'string',
                ]);
            }
        }

        // Seed default currency (USD)
        \App\Models\Currency::updateOrCreate([
            'code' => 'USD',
        ], [
            'prefix' => '$',
            'suffix' => '',
            'format' => '1.000,00',
        ]);

        \App\Providers\SettingsProvider::flushCache();

        $this->call([
            CustomPropertySeeder::class,
            EmailTemplateSeeder::class,
        ]);
    }
}
