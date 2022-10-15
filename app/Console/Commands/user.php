<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class user extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--1=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a user on the system via the CLI.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $first = $this->option('1') ?? $this->confirm(trans('user.first'));

        // $user = $this->creationService->handle(compact('first'));
        echo $this->table(['Field', 'Value'], [
            ['1', $first ? 'Yes' : 'No'],
        ]);
    }
}
