<?php

namespace App\Console\Commands\Settings;

use App\Models\Setting;
use Illuminate\Console\Command;

class ChangeTheme extends Command
{
    /**
     * Change the theme for the application
     *
     * @var string
     */
    protected $signature = 'p:settings:change-theme {theme?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the theme for the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $currentTheme = Setting::where('key', 'theme')->first();
        // List all theme from the themes folder
        $themes = array_diff(scandir(base_path('themes')), array('.', '..'));
        // Remove count from the array
        $themes = array_values($themes);
        // If the theme is not specified, list all themes and ask the user to choose one
        if (!$this->argument('theme')) {
            $this->info('Current theme: ' . $currentTheme->value);
            $theme = $this->choice('Which theme do you want to use?', $themes);
        } else {
            $theme = $this->argument('theme');
        }
        // Check if the theme exists
        if (!in_array($theme, $themes)) {
            $this->error('The theme ' . $theme . ' does not exist');
            return Command::FAILURE;
        }
        // Change the theme
        $currentTheme->value = $theme;
        $currentTheme->save();

        $this->info('Theme changed successfully');

        return Command::SUCCESS;
    }
}
