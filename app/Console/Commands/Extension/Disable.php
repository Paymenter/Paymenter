<?php

namespace App\Console\Commands\Extension;

use App\Models\Extension;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\select;

class Disable extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extension:disable {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable a extension if it causes issues';

    public function handle()
    {
        $name = $this->argument('name');
        $extension = Extension::where('extension', $name)->first();
        if (!$extension) {
            $this->error('Extension not found');

            return;
        }
        $extension->update(['enabled' => false]);
        $this->info('Extension disabled');
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array
     */
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => fn () => select(
                label: 'Which extension do you want to disable?',
                options: Extension::all()->pluck('extension', 'extension')->toArray(),
            ),
        ];
    }
}
