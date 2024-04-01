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
        $this->availableSettings = config('available-settings');
        $this->settings = json_decode(json_encode($this->availableSettings));
    }

    public function save()
    {
        $this->validate();

        foreach ($this->fields as $group => $settings) {
            foreach ($settings as $key => $setting) {
                // Get only the settings that have changed
                if ($setting !== config("settings.$key")) {
                    Setting::where('settingable_type', null)->where('key', $key)->update(['value' => $setting]);
                }
            }
        }

        SettingsProvider::flushCache();

        $this->dispatch('saved');
    }

    public function validationAttributes()
    {
        $attributes = [];
        foreach ($this->availableSettings as $group => $settings) {
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
        foreach ($this->availableSettings as $group => $settings) {
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
