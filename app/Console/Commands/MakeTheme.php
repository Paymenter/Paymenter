<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Filesystem\Filesystem;
use Qirolab\Theme\Theme;

class MakeTheme extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:theme:create {name} {author}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the theme's name in kebab case
        $theme_name = str_replace(' ', '-', (strtolower($this->argument('name'))));
        if ($this->themeExists($theme_name)) {
            return $this->error("Theme \"$theme_name\" already exists.");
        }

        $author = $this->argument('author');

        $themes_directory = 'themes/';

        $fs = new Filesystem;

        // Copy files from `default` theme
        $fs->copyDirectory(
            $themes_directory . 'default',
            $themes_directory . $theme_name
        );
        $this->line('[1/4] Copied files from `default` theme.');

        // Replace all themes/default to themes/$theme_name in config files
        // `/` at the end is present because we don't want to replace `default` in `export default defineConfig`
        $fs->replaceInFile(
            'default/',
            "$theme_name/",
            $themes_directory . $theme_name . DIRECTORY_SEPARATOR . 'vite.config.js',
        );

        $this->line('[2/4] Replaced path values in vite.config.js.');

        // Update author and description fields in new `theme.php`
        $theme_file = $themes_directory . $theme_name . DIRECTORY_SEPARATOR . 'theme.php';
        // Get the contents of the file
        $theme_file_contents = $fs->get($theme_file);
        // Replace theme name and author
        $theme_file_contents = str_replace(
            ["'name' => 'Default'", "'author' => 'Paymenter'"],
            ["'name' => '$theme_name'", "'author' => '$author'"],
            $theme_file_contents
        );

        // Save the changes back to the file
        $fs->put($theme_file, $theme_file_contents);

        $this->line('[3/4] Replaced variables in `theme.php`.');

        $this->info("[4/4] Theme \"$theme_name\" created successfully.");
        $this->newLine();

        $this->comment('You can now start developing your theme by running:');
        $this->comment("    `npm run dev $theme_name`");
        $this->comment('After you have finished developing, you can build your theme by running:');
        $this->comment("    `npm run build $theme_name`");

        return Command::SUCCESS;
    }

    protected function themeExists(string $theme): bool
    {
        $directory = 'themes/' . $theme;

        if (is_dir($directory)) {
            return true;
        }

        return false;
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => ['What should be the Theme\'s name?', 'E.g. My Theme'],
            'author' => ['Who is the author of this theme?', 'E.g. John Doe <johndoe@example.com>'],
        ];
    }
}
