<?php

namespace App\Console\Commands\Extension;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extension:install {type} {name}';

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
        if (method_exists($extensionInstance, 'installed')) {
            try {
                $extensionInstance->installed();
            } catch (\Exception $e) {
                Log::error("Error during installation of extension {$this->argument('name')}: " . $e->getMessage());

                return $this->error('An error occurred while installing the extension: ' . $e->getMessage());
            }
        }
    }
}
