<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Providers\SettingsProvider;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Settings extends Component
{
    public $fields;

    private $availableSettings;

    #[Locked]
    public $settings;

    public function boot()
    {
        $this->settings = \App\Classes\Settings::settingsObject();
    }

    public function save()
    {
        $this->authorize('admin.settings.update');
        $this->validate();

        foreach ($this->fields as $group => $settings) {
            foreach ($settings as $key => $setting) {
                // Get only the settings that have changed
                if ($setting !== config("settings.$key")) {
                    $modelSetting = Setting::where('settingable_type', null)->where('key', $key)->update(['value' => $setting]);
                    if (!$modelSetting) {
                        $avSetting = \App\Classes\Settings::getSetting($key);
                        Setting::create([
                            'key' => $key,
                            'value' => $setting,
                            'settingable_type' => null,
                            'type' => $avSetting['database_type'] ?? 'string',
                            'encrypted' => $avSetting['encrypted'] ?? false,
                        ]);
                    }
                }
            }
        }

        SettingsProvider::flushCache();

        $this->dispatch('saved');
    }

    public function validationAttributes()
    {
        $attributes = [];
        foreach (\App\Classes\Settings::settings() as $group => $settings) {
            foreach ($settings as $setting) {
                $attributes += ["fields.{$group}.{$setting['name']}" => __($setting['label'] ?? $setting['name'])];
            }
        }
        return $attributes;
    }


    public function rules()
    {

        $rules = [];

        foreach ($this->settings as $group => $setting) {
            foreach ($setting as $item) {
                if (isset($item->required) && $item->required) {
                    $rules["fields.{$group}.{$item->name}"] = 'required';
                    if (isset($item->validation)) {
                        $rules["fields.{$group}.{$item->name}"] .= '|' . $item->validation;
                    }
                } else if (isset($item->validation)) {
                    $rules["fields.{$group}.{$item->name}"] = 'nullable|' . $item->validation;
                }
            }
        }

        return $rules;
    }


    public function mount()
    {
        foreach (\App\Classes\Settings::settings() as $group => $settings) {
            foreach ($settings as $setting) {
                $this->fill([
                    "fields.{$group}.{$setting['name']}" => config("settings.{$setting['name']}")
                ]);
            }
        }
    }

    public function render()
    {
        return view('admin.settings');
    }
}
