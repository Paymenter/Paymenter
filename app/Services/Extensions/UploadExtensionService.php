<?php

namespace App\Services\Extensions;

use App\Classes\Extension\Extension;
use App\Classes\Extension\Gateway;
use App\Classes\Extension\Server;
use Illuminate\Support\Facades\File;

class UploadExtensionService
{
    /**
     * Handle the uploaded extension file. 
     * The added file is always a zip file.
     * 
     * @param string $filePath
     * @return void
     */
    public function handle(string $filePath)
    {
        // Validate the file type and size
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception('File does not exist or is not readable.');
        }
        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'zip') {
            throw new \Exception('Invalid file type. Only zip files are allowed.');
        }

        // Extract the zip file
        $extractPath = storage_path('app/extensions/' . uniqid());

        if (!is_dir($extractPath) && !mkdir($extractPath, 0755, true)) {
            throw new \Exception('Failed to create extraction directory.');
        }

        $this->unzip($filePath, $extractPath);

        // Define if the folder path is correct or we need to traverse it (based on .php files)
        $path = $this->validateExtensionPath($extractPath);

        // Find the php file extending either Extension, Server, or Gateway
        $type = $this->getExtensionType($path);

        // Move the files to the correct location
        $destinationPath = base_path('extensions/' . ucfirst($type['type']) . 's/' . $type['class']);
        // Check if destination directory exists, if so, remove it
        if (is_dir($destinationPath)) {
            File::deleteDirectory($destinationPath);
        }

        if (!rename($path, $destinationPath)) {
            throw new \Exception('Failed to move the extension files to the destination.');
        }


        // Remove the extracted files
        File::deleteDirectory($extractPath);
    }

    private function getExtensionType(string $path): array
    {
        $files = glob($path . '/*.php');
        $type = ['class' => null, 'type' => null];
        foreach ($files as $file) {
            // Read file
            $content = file_get_contents($file);
            if (preg_match('/namespace\s+(.+?);/', $content, $matches)) {
                require_once $file; // Include the file to load the class
                $namespace = $matches[1];
                if (preg_match('/class\s+(\w+)/', $content, $classMatches)) {
                    $className = $classMatches[1];
                    $fullClassName = $namespace . '\\' . $className;

                    // Only return className
                    $type['class'] = $className;
                    if (is_subclass_of($fullClassName, Server::class)) {
                        $type['type'] = 'server';
                    } elseif (is_subclass_of($fullClassName, Gateway::class)) {
                        $type['type'] = 'gateway';
                    } elseif (is_subclass_of($fullClassName, Extension::class)) {
                        $type['type'] = 'other';
                    }
                }
            }
        }
        if (!$type['class'] || !$type['type']) {
            throw new \Exception('No valid extension class found in the provided path.');
        }
        return $type;
    }

    private function validateExtensionPath(string $path, int $depth = 0): string
    {
        if ($depth > 1) {
            throw new \Exception('Maximum depth reached while validating extension path.');
        }
        // Check if the path contains a valid extension structure
        $files = glob($path . '/*.php');

        if (empty($files)) {
            // Retry it ONCE with the first subdirectory
            $subDirs = glob($path . '/*', GLOB_ONLYDIR);
            if (count($subDirs) > 0) {
                for ($i = 0; $i < count($subDirs); $i++) {
                    if (basename($subDirs[$i]) === '__MACOSX') {
                        continue;
                    }

                    if (glob($subDirs[$i] . '/*.php')) {
                        $newPath = $subDirs[$i];
                        break;
                    }
                }
                if (isset($newPath)) {
                    // Pass the new path with increased depth
                    return $this->validateExtensionPath($newPath, $depth + 1);
                }
            }

            throw new \Exception('No valid extension files found in the provided path.');
        }

        // Return the path if it contains valid PHP files
        return $path;
    }

    private function unzip(string $filePath, string $extractPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();

            // Remove the zip file after extraction
            File::delete($filePath);
        } else {
            throw new \Exception('Failed to open the zip file.');
        }
    }
}
