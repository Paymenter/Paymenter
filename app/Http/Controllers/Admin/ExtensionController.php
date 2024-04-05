<?php

namespace App\Http\Controllers\Admin;

use App\Models\Extension;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class ExtensionController extends Controller
{
    public function index()
    {
        $servers = scandir(base_path('app/Extensions/Servers/'));
        $gateways = scandir(base_path('app/Extensions/Gateways/'));
        $events = scandir(base_path('app/Extensions/Events/'));
        foreach ($servers as $key => $server) {
            if ($server == '.' || $server == '..') {
                continue;
            }
            // Check if the extension is enabled
            $extension = Extension::where('name', $server)->first();
            if (!$extension) {
                $extension = new Extension();
                $extension->name = $server;
                $extension->type = 'server';
                $extension->enabled = 0;
                $extension->save();
            }
        }
        foreach ($gateways as $key => $gateway) {
            if ($gateway == '.' || $gateway == '..') {
                continue;
            }
            // Check if the extension is enabled
            $extension = Extension::where('name', $gateway)->first();
            if (!$extension) {
                $extension = new Extension();
                $extension->name = $gateway;
                $extension->type = 'gateway';
                $extension->enabled = 0;
                $extension->save();
            }
        }
        foreach ($events as $key => $event) {
            if ($event == '.' || $event == '..') {
                continue;
            }
            // Check if the extension is enabled
            $extension = Extension::where('name', $event)->first();
            if (!$extension) {
                $extension = new Extension();
                $extension->name = $event;
                $extension->type = 'event';
                $extension->enabled = 0;
                $extension->save();
            }
        }

        return view('admin.extensions.index', compact('servers', 'gateways', 'events'));
    }

    /**
     * Browse extensions via the marketplace API
     *
     */
    public function browse(Request $request)
    {
        if ($request->page) {
            $page = $request->page;
        } else {
            $page = 1;
        }
        $url = config('app.marketplace') . 'extensions?page=' . $page . '&version=' . config('app.version') . '&search=' . $request->search;
        $response = Http::get($url)->json();
        $extensions = $response['data'];

        return view('admin.extensions.browse', compact('extensions'));
    }

    /**
     * Install extension from the marketplace
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function install($id)
    {
        $url = config('app.marketplace') . 'extensions/' . $id . '?version=' . config('app.version');
        $response = Http::get($url);
        if (!$response->successful()) {
            return redirect()->route('admin.extensions.browse')->with('error', 'Extension not found');
        }
        $extension = $response->json();
        $path = base_path('app/Extensions/' . ucfirst($extension['type']) . 's/' . $extension['name']);
        if ($extension['type'] == 'theme') {
            $path = base_path('themes/' . $extension['name']);
        }
        if (file_exists($path)) {
            //Remove the folder
            $this->deleteDir($path);
        }
        mkdir($path, 0777, true);
        // Check if temp folder exists
        if (!file_exists(base_path('storage/app/temp'))) {
            mkdir(base_path('storage/app/temp'), 0777, true);
        }
        // Download zip to temp folder
        $zip = Http::get(config('app.marketplace') . 'extensions/' . $id . '/download?version=' . config('app.version'));
        $zip = $zip->body();
        $zipPath = base_path('storage/app/temp/' . $extension['name'] . '.zip');
        file_put_contents($zipPath, $zip);
        // Unzip
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        $zip->extractTo($path);
        $zip->close();
        // Delete zip
        unlink($zipPath);

        // If it unzips in a subfolder, move the files to the root
        $subfolder = scandir($path);
        if (count($subfolder) == 3) {
            $subfolder = $subfolder[2];
            if (is_dir($path . '/' . $subfolder)) {
                $subfolderPath = $path . '/' . $subfolder;
                $files = scandir($subfolderPath);
                foreach ($files as $file) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    rename($subfolderPath . '/' . $file, $path . '/' . $file);
                }
                rmdir($subfolderPath);
            }
        }

        // Remove temp folder
        $this->deleteDir(base_path('storage/app/temp'));

        // Check if the extension is enabled
        $extensionModel = Extension::where('name', $extension['name'])->first();
        if (!$extensionModel) {
            $extensionModel = new Extension();
            $extensionModel->name = $extension['name'];
            $extensionModel->type = $extension['type'];
            $extensionModel->enabled = 0;
            $extensionModel->save();
        }
        return redirect()->route('admin.extensions')->with('success', 'Extension downloaded successfully');
    }

    /**
     * Delete a directory recursively
     *
     * @param $dir
     *
     * @return bool
     */
    private function deleteDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDir($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }


    public function edit($sort, $name)
    {
        $extension = Extension::where('name', $name)->first();
        if (!$extension) {
            // Check if class exists
            if (!class_exists('App\Extensions\\' . ucfirst($sort) . 's\\' . $name . '\\' . $name)) {
                return redirect()->route('admin.extensions')->with('error', 'Extension not found');
            }
            Extension::create([
                'name' => $name,
                'enabled' => false,
                'type' => $sort,
            ]);
            $extension = Extension::where('name', $name)->first();
        }
        $namespace = "App\Extensions\\" . ucfirst($sort) . "s\\" . $name . "\\" . $name;

        $extension->config = json_decode(json_encode((new $namespace($extension))->getConfig()));
        $extension->name = $name;

        $extensionConfig = $extension->getConfig()->get();
        foreach ($extension->config as $key => $config) {
            $config->value = $extensionConfig->where('key', $config->name)->first()->value ?? null;
            try {
                $config->value = Crypt::decryptString($config->value);
            } catch (DecryptException $e) {
            }
        }

        $metadata = ExtensionHelper::getMetadata(Extension::where('name', $name)->first());

        return view('admin.extensions.edit', compact('extension', 'metadata'));
    }

    public function update(Request $request, $sort, $name)
    {
        $extension = Extension::where('name', $name)->first();
        if (!$extension) {
            // Check if class exists
            if (!class_exists('App\\Extensions\\' . ucfirst($sort) . 's\\' . $name . '\\' . $name)) {
                return redirect()->route('admin.extensions')->with('error', 'Extension not found');
            }
            Extension::create([
                'name' => $name,
                'enabled' => false,
                'type' => $sort,
            ]);
            $extension = Extension::where('name', $name)->first();
        }
        $extension->update(['enabled' => $request->input('enabled'), 'display_name' => $request->input('display_name')]);
        $config = ExtensionHelper::updateConfig($extension, $request);
        if ($config instanceof \Illuminate\Http\RedirectResponse) {
            return $config;
        }


        return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
    }

    public function updateExtension(Extension $extension){
        // Get ID from the marketplace
        $url = config('app.marketplace') . 'extensions?version=' . config('app.version') . '&search=' . $extension->name;
        $response = Http::get($url)->json();

        if (isset($response['error']) || count($response['data']) == 0) {
            return redirect()->route('admin.extensions')->with('error', 'Extension not found');
        }
        $extensionId = $response['data'][0]['id'];

        $this->install($extensionId);

        $extension->update_available = null;
        $extension->save();

        return redirect()->route('admin.extensions')->with('success', 'Extension updated successfully');
    }
}
