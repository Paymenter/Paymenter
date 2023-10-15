<?php

namespace App\Console\Commands\Settings;

use App\Models\Setting;
use Illuminate\Console\Command;

class DisableRecaptcha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:settings:disable-recaptcha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable recaptcha for the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $recaptcha = Setting::where('key', 'recaptcha')->first();
        $recaptcha->value = 0;
        $recaptcha->save();

        $this->info('Recaptcha disabled successfully');

        return Command::SUCCESS;
    }
}
