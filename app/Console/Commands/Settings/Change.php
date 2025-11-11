<?php

namespace App\Console\Commands\Settings;

use App\Classes\Settings;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

use function Laravel\Prompts\form;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class Change extends Command
{
    protected $signature = 'app:settings:change {key?} {value?}';

    protected $description = 'Change a setting';

    public function handle()
    {
        Config::set('audit.console', true);

        $key = $this->argument('key');
        $value = $this->argument('value');
        $form = form();

        if (!$key) {
            // Settings::settings is a array with first level keys as categories and second level keys as settings, so we need to flatten it
            $settings = collect(Settings::settings())->flatten(1)->map(function ($item) {
                return $item['name'];
            })->toArray();

            $form->suggest('Which setting would you like to change?', $settings, name: 'key');
        }

        if (!$value) {
            $form->add(function ($responses) use ($key) {
                $key = $responses['key'] ?? $key;
                $setting = Settings::getSetting($key);
                if (!isset($setting->type)) {
                    return text('What value should the setting have?', default: '', hint: 'Could not find setting but you can still change it');
                }
                // What type is the setting?
                if ($setting->type === 'select') {
                    return select('What value should the setting have?', $setting->options);
                } else {
                    return text('What value should the setting have?', default: $setting->value ?? '');
                }
            });
        }
        $form = $form->submit();

        if (isset($form['key'])) {
            $key = $form['key'];
        }
        if (isset($form[0]) || isset($form[1])) {
            $value = $form[0] ?? $form[1];
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
