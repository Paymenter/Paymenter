<?php

namespace Database\Seeders;

use App\Classes\Settings;
use App\Models\Currency;
use App\Models\Role;
use App\Models\Setting;
use App\Providers\SettingsProvider;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Role::updateOrCreate(['name' => 'admin'], ['permissions' => ['*']]);

        foreach (Settings::settings() as $settings) {
            foreach ($settings as $setting) {
                if (!isset($setting['default'])) {
                    continue;
                }
                if (in_array($setting['name'], ['mail_header', 'mail_footer', 'mail_css'])) {
                    // Read from file in ./data/<name>
                    $setting['default'] = file_get_contents(__DIR__ . '/data/' . $setting['name']);
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
        if (Currency::count() === 0) {
            Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'prefix' => '$',
                'suffix' => '',
                'format' => '1,000.00',
            ]);
        }

        SettingsProvider::flushCache();

        $this->call([
            EmailTemplateSeeder::class,
        ]);
    }
}
