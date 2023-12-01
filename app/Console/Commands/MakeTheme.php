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
    protected $signature = 'p:make-theme {name} {author} {description}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate base files for your theme.';

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
        $description = $this->argument('description');

        $themes_directory = config('theme.base_path') . DIRECTORY_SEPARATOR;

        $fs = new Filesystem;

        // Copy files from `default` theme
        $fs->copyDirectory(
            $themes_directory . 'default',
            $themes_directory . $theme_name
        );
        $this->line("[1/4] Copied files from `default` theme.");

        $config_files = ['vite.config.js', 'tailwind.config.js'];
        foreach ($config_files as $config_file) {
            // Replace all themes/default to themes/$theme_name in config files
            // `/` at the end is present because we don't want to replace `default` in `export default defineConfig`
            $fs->replaceInFile(
                'default/',
                "$theme_name/",
                $themes_directory . $theme_name . DIRECTORY_SEPARATOR . $config_file,
            );
        }
        $this->line("[2/4] Replaced path values in config files.");

        // Update author and description fields in new `theme.json`
        $theme_json_path = $themes_directory . $theme_name . DIRECTORY_SEPARATOR . 'theme.json';
        $theme_json = json_decode(
            $fs->get($theme_json_path),
            true
        );

        $theme_json['author'] = $author;
        $theme_json['description'] = $description;

        $fs->put(
            $theme_json_path,
            json_encode($theme_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        $this->line("[3/4] Replaced variables in `theme.json`.");

        $this->info("[4/4] Theme \"$theme_name\" created successfully.");
        $this->newLine();

        $this->comment("You can now start developing your theme by running:");
        $this->comment("    `npm run dev $theme_name`");
        $this->comment("After you have finished developing, you can build your theme by running:");
        $this->comment("    `npm run build $theme_name`");

        return Command::SUCCESS;
    }

    protected function themeExists(string $theme): bool
    {
        $directory = config('theme.base_path') . DIRECTORY_SEPARATOR . $theme;

        if (is_dir($directory)) {
            return true;
        }

        return false;
    }

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'name' => ['What should be the Theme\'s name?', "E.g. My Theme"],
            'author' => ['Who is the author of this theme?', "E.g. John Doe <johndoe@example.com>"],
            'description'  => ['What is the description of this theme?', 'A really cool theme for paymenter.'],
        ];
    }
}
