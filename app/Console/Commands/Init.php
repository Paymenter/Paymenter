<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\text;

class Init extends Command implements PromptsForMissingInput
{
    protected $signature = 'app:init {name} {url}';

    protected $description = 'Show the application initialization steps';

    public function handle()
    {
        $this->info('Thanks for installing Paymenter!');

        // Validate the URL
        if (!str_starts_with($this->argument('url'), 'http')) {
            $this->error('The URL must start with http or https.');

            return;
        }

        Setting::updateOrCreate(['key' => 'company_name'], ['value' => $this->argument('name')]);
        Setting::updateOrCreate(['key' => 'app_url'], ['value' => rtrim($this->argument('url'), '/')]);

        $this->info("Now you're all set up!\nVisit Paymenter at " . $this->argument('url'));
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => 'What is the name of your company?',
            'url' => fn () => text('What is the URL of your application?', required: true, validate: function ($value) {
                return str_starts_with($value, 'http') ? null : 'The URL must start with http or https.';
            }),
        ];
    }
}
