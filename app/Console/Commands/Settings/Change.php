<?php

namespace App\Console\Commands\Settings;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class Change extends Command implements PromptsForMissingInput
{
    protected $signature = 'app:settings:change {key} {value}';

    protected $description = 'Change a setting';

    public function handle()
    {
        Setting::updateOrCreate(
            ['key' => $this->argument('key')],
            ['value' => $this->argument('value')]
        );
    }

    protected function promptForMissingArgumentsUsing(): array
    {   
        return [
            'key' => 'What is the setting key? (e.g. app_url)',
            'value' => 'What is the setting value? (e.g. https://example.com)',
        ];
    }
}