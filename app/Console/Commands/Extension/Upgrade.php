<?php

namespace App\Console\Commands\Extension;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Upgrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extension:upgrade {type} {name} {oldVersion?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Internal command used to call upgrade on an extension';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $extensionClass = 'Paymenter\\Extensions\\' . ucfirst($this->argument('type')) . 's\\' . ucfirst($this->argument('name')) . '\\' . ucfirst($this->argument('name'));
        if (!class_exists($extensionClass)) {
            return $this->error("The extension class {$extensionClass} does not exist.");
        }

        $extensionInstance = new $extensionClass;
        if (method_exists($extensionInstance, 'upgraded')) {
            try {
                $extensionInstance->upgraded($this->argument('oldVersion'));
            } catch (\Exception $e) {
                Log::error("Error during upgrade of extension {$this->argument('name')}: " . $e->getMessage());

                return $this->error('An error occurred while upgrading the extension: ' . $e->getMessage());
            }
        }
    }
}
