<?php

namespace App\Console\Commands\Extension;

use App\Models\Extension;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class Create extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extension:create {name} {type} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new extension';

    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->argument('type');
        if (!in_array($type, ['server', 'gateway', 'other'])) {
            $this->error('Invalid extension type. Valid types are: server, gateway, event');

            return;
        }
        // Read stub file contents
        $stub = file_get_contents(__DIR__ . '/stubs/' . $type . '.stub');
        // Replace placeholders with actual values
        $stub = str_replace('{{ class }}', ucfirst($name), $stub);
        $stub = str_replace('{{ namespace }}', 'Paymenter\\Extensions\\' . ucfirst($type) . 's' . '\\' . ucfirst($name), $stub);

        // Create the extension file
        $path = base_path('extensions/' . ucfirst($type) . 's/' . ucfirst($name) . '/' . ucfirst($name) . '.php');
        if (file_exists(dirname($path)) && !$this->option('force')) {
            $this->error("Extension already exists\nRerun the command with a different name or use --force to overwrite");

            return;
        }
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $stub);

        // Return path
        $this->info("Extension created at: {$path}");
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => 'What is the name of the extension?',
            'type' => function () {
                $type = $this->choice('What type of extension?', ['server', 'gateway', 'other']);

                return $type;
            },
        ];
    }
}
