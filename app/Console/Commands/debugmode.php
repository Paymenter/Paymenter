<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Artisan;

class debugmode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable debugmode';

    /**
     * Execute the console command.
     * 
     * @return int
     */
    public function handle()
    {   
        $envFilePath = base_path('.env');

        if (!file_exists($envFilePath)) {
            $this->error('.env file not found.');
            return;
        }

        $envContents = file_get_contents($envFilePath);

        $question = $this->choice('What do you want to do with debugmode', [
            'Enable it',
            'Disable it',
            'Cancel command',
        ]);

        if ($question === 'Enable it') {
            Artisan::call('down');
            $envContents = str_replace('APP_DEBUG=false', 'APP_DEBUG=true', $envContents);
            $this->info('Debug mode enabled');
            Artisan::call('up');
        } elseif ($question === 'Disable it') {
            Artisan::call('down');
            $envContents = str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $envContents);
            $this->info('Debug mode disabled');
            Artisan::call('up');
        } 
        elseif ($question === 'Cancel command') {
            $this->info('Command succesfully canceled');
        }else {
            $this->error('Something went wrong. Debug mode is unchanged.');
        } 

        file_put_contents($envFilePath, $envContents);
    }
}
