<?php

namespace App\Console\Commands\Extensions;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;


class Create extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:extension:create 
    {name} 
    {type}
    {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new extension';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->argument('type');

        if (!in_array($type, ['server', 'gateway', 'event', 'domainRegister',  'other'])) {
            $this->error('Invalid extension type. Valid types are: server, gateway, event, domainRegister');
            return;
        }

        // Define the path
        $basePath = app_path("Extensions/" . ucfirst($type) . "s/" . ucfirst($name));
        $path = "{$basePath}/" . ucfirst($name) . '.php';

        if (file_exists($path) && !$this->option('force')) {
            $this->error("Extension already exists\nRerun the command with a different name or use --force to overwrite");
            return;
        }

        $stubPath = __DIR__ . "/stubs/{$type}.stub";
        $this->createExtensionFile($name, $type, $path, $stubPath);

        // Handle other type specifically
        if ($type === 'other') {
            // Create the routes.php file
            $pathRoutes = "{$basePath}/routes.php";
            $stubPathRoutes = __DIR__ . "/stubs/routes.stub";
            $this->createExtensionFile($name, $type, $pathRoutes, $stubPathRoutes);

            // Create the welcome.blade.php file
            $pathWelcome = "{$basePath}/views/admin/welcome.blade.php";
            $stubPathWelcome = __DIR__ . "/stubs/views/admin.welcome.stub";
            $this->createExtensionFile($name, $type, $pathWelcome, $stubPathWelcome);

            $pathWelcome = "{$basePath}/views/clients/welcome.blade.php";
            $stubPathWelcome = __DIR__ . "/stubs/views/clients.welcome.stub";
            $this->createExtensionFile($name, $type, $pathWelcome, $stubPathWelcome);
        }

        $this->info("Extension '{$name}' of type '{$type}' created successfully.");
    }


    /**
     * @param string $name
     * @param string $path
     * @param string $type
     * @param string $stubPath
     * @return void
     */
    private function createExtensionFile($name, $type, $path, $stubPath)
    {
        $stub = file_get_contents($stubPath);

        // Replace placeholders with actual values
        $stub = str_replace('{{ class }}', ucfirst($name), $stub);
        $stub = str_replace('{{ namespace }}', "App\\Extensions\\" . ucfirst($type) . "s\\" . ucfirst($name), $stub);

        // Create the extension file
        if (!file_exists(dirname($path))) mkdir(dirname($path), 0755, true);
        file_put_contents($path, $stub);
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
                $type = $this->choice('What type of extension?', ['server', 'gateway', 'event', 'domainRegister', 'other']);

                return $type;
            },
        ];
    }
}
