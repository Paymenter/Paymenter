<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Extension;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DeleteExtensionInteractive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extension:delete-interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively find and delete an extension from the database by its ID.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fetching extensions from the database...');

        if (!Schema::hasTable('extensions')) {
            $this->error('The `extensions` table does not exist. Please run migrations.');
            return 1;
        }

        $extensions = Extension::all();

        if ($extensions->isEmpty()) {
            $this->warn('No extensions found in the database.');
            return 0;
        }

        $this->line('Available Extensions:');
        $headers = ['ID', 'Name', 'Extension Folder', 'Type', 'Enabled'];
        $extensionsData = $extensions->map(function ($ext) {
            return [
                'ID' => $ext->id,
                'Name' => $ext->name,
                'Extension Folder' => $ext->extension,
                'Type' => $ext->type,
                'Enabled' => $ext->enabled ? 'Yes' : 'No',
            ];
        });

        $this->table($headers, $extensionsData);

        $extensionId = $this->ask('Please enter the ID of the extension you want to delete');

        $extensionToDelete = Extension::find($extensionId);

        if (!$extensionToDelete) {
            $this->error("Extension with ID `{$extensionId}` not found.");
            return 1;
        }

        $this->warn("You are about to delete the following extension:");
        $this->table(
            $headers,
            [[
                'ID' => $extensionToDelete->id,
                'Name' => $extensionToDelete->name,
                'Extension Folder' => $extensionToDelete->extension,
                'Type' => $extensionToDelete->type,
                'Enabled' => $extensionToDelete->enabled ? 'Yes' : 'No',
            ]]
        );

        if ($this->confirm('Are you sure you want to permanently delete this extension record?')) {
            try {
                // Determine extension path
                $typeFolder = ucfirst($extensionToDelete->type) . 's'; // Gateways, Servers, Others
                $extensionPath = base_path("extensions/{$typeFolder}/" . $extensionToDelete->extension);

                // Check if files exist and ask to delete them
                if (File::isDirectory($extensionPath)) {
                    if ($this->confirm("Do you want to delete the files for the extension '{$extensionToDelete->name}'?")) {
                        File::deleteDirectory($extensionPath);
                        $this->info('Extension files deleted.');
                    } else {
                        $this->info('Skipping file deletion.');
                    }
                }

                $extensionToDelete->delete();
                $this->info("Successfully deleted extension `{$extensionToDelete->name}` (ID: {$extensionId}).");

                // Clear cache to apply changes
                $this->call('cache:clear');

            } catch (\Exception $e) {
                $this->error('An error occurred while deleting the extension: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('Deletion cancelled.');
        }

        return 0;
    }
}