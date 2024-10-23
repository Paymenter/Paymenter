<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class Init extends Command
{
    protected $signature = 'app:init';

    protected $description = 'Show the application initialization steps';

    public function handle()
    {
        $this->info("Thanks for installing Paymenter!\nLets gets started by providing the following information:");

        $app_name = text('What is the name of your company?', required: true, placeholder: 'Paymenter');
        $app_url = text('What is the URL of your application?', required: true, placeholder: 'https://paymenter.org');

        Setting::updateOrCreate(['key' => 'company_name'], ['value' => $app_name]);
        Setting::updateOrCreate(['key' => 'app_url'], ['value' => rtrim($app_url, '/')]);

        $this->info("Great now you're all set up!\nVisit Paymenter at $app_url");
    }
}