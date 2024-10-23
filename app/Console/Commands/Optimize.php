<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Optimize extends Command
{
    protected $signature = 'app:optimize';

    protected $description = 'Optimize the application';

    public function handle()
    {
        $this->call('optimize');
        $this->call('filament:optimize');
    }
}
