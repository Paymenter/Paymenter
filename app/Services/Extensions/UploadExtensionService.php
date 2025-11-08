<?php

namespace App\Services\Extensions;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Extension;
use App\Classes\Extension\Gateway;
use App\Classes\Extension\Server;
use App\Console\Commands\Extension\Install;
use App\Console\Commands\Extension\Upgrade;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class UploadExtensionService
{
    /**
     * Handle the uploaded extension file.
     * The added file is always a zip file.
     *
     * @return void
     */
    public function handle(string $filePath): string
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

        try {
            // Define if the folder path is correct or we need to traverse it (based on .php files)
            $path = $this->validateExtensionPath($extractPath);

            // Find the php file extending either Extension, Server, or Gateway
            $type = $this->getExtensionType($path);

            // Move the files to the correct location
            $destinationPath = base_path('extensions/' . ucfirst($type['type']) . 's/' . $type['class']);
            $updating = false;
            $oldVersion = null;

            // Check if destination directory exists, if so, remove it
            if (is_dir($destinationPath)) {
                $updating = true;
            }

            if ($updating) {
                // Read the extension class for current version
                $extensionClass = 'Paymenter\\Extensions\\' . ucfirst($type['type']) . 's\\' . ucfirst($type['class']);
                if (class_exists($extensionClass)) {
                    $reflection = new ReflectionClass($extensionClass);
                    $attributes = $reflection->getAttributes(ExtensionMeta::class);

                    if (count($attributes) > 0) {
                        $extensionMeta = $attributes[0]->newInstance();
                        if ($extensionMeta->version) {
                            $oldVersion = $extensionMeta->version;
                        }
                    }
                }
                File::deleteDirectory($destinationPath);
            }

            if (!rename($path, $destinationPath)) {
                throw new \Exception('Failed to move the extension files to the destination.');
            }
        } catch (\Exception $e) {
            // Clean up the extracted files in case of an error
            File::deleteDirectory($extractPath);
            throw $e; // Re-throw the exception after cleanup
        }

        // Remove the extracted files
        File::deleteDirectory($extractPath);

        // Execute the upgraded method if it exists
        if ($updating) {
            Artisan::call(Upgrade::class, [
                'type' => $type['type'],
                'name' => $type['class'],
                'oldVersion' => $oldVersion,
            ]);
        } else {
            Artisan::call(Install::class, [
                'type' => $type['type'],
                'name' => $type['class'],
            ]);
        }

        return $type['type'];
    }

    private function getExtensionType(string $path): array
    {
        $files = glob($path . '/*.php');
        $type = ['class' => null, 'type' => null];
        foreach ($files as $file) {
            // Read file
            $content = file_get_contents($file);

            // Security: Validate file content before processing
            if (!$this->validatePhpFile($content)) {
                continue;
            }

            if (preg_match('/namespace\s+(.+?);/', $content, $matches)) {
                $namespace = $matches[1];

                // Security: Validate namespace format
                if (!preg_match('/^[A-Za-z_\\\\][A-Za-z0-9_\\\\]*$/', $namespace)) {
                    continue;
                }

                if (preg_match('/^\s*class\s+([A-Za-z_][A-Za-z0-9_]*)\s*(?:extends|implements|\{)/m', $content, $classMatches)) {
                    $className = $classMatches[1];
                    $fullClassName = $namespace . '\\' . $className;

                    // Only return className
                    $type['class'] = $className;

                    // Security: Check inheritance through static analysis instead of loading the file
                    if ($this->extendsClass($content, 'Server')) {
                        $type['type'] = 'server';
                    } elseif ($this->extendsClass($content, 'Gateway')) {
                        $type['type'] = 'gateway';
                    } elseif ($this->extendsClass($content, 'Extension')) {
                        $type['type'] = 'other';
                    }

                    if ($type['class'] && $type['type']) {
                        break; // Exit the loop if we found a valid class
                    }
                }
            }
        }
        if (!$type['class'] || !$type['type']) {
            throw new \Exception('No valid extension class found in the provided path.');
        }

        return $type;
    }

    /**
     * Validate PHP file content for security
     */
    private function validatePhpFile(string $content): bool
    {
        // Check for dangerous functions
        $dangerousFunctions = [
            'eval', 'exec', 'shell_exec', 'system', 'passthru',
            'proc_open', 'popen', 'curl_exec', 'curl_multi_exec',
            'parse_ini_file', 'show_source', 'file_get_contents',
            'file_put_contents', 'unlink', 'rmdir'
        ];

        foreach ($dangerousFunctions as $func) {
            if (preg_match('/\b' . preg_quote($func, '/') . '\s*\(/i', $content)) {
                throw new \Exception("Extension contains potentially dangerous function: {$func}");
            }
        }

        return true;
    }

    /**
     * Check if class extends a specific parent class through static analysis
     */
    private function extendsClass(string $content, string $parentClass): bool
    {
        return preg_match('/class\s+\w+\s+extends\s+' . preg_quote($parentClass, '/') . '\b/', $content) === 1;
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
        $zip = new \ZipArchive;
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
