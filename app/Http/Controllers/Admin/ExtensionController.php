<?php

namespace App\Http\Controllers\Admin;

use App\Models\Extension;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
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
    public function install(Request $request, $id)
    {
        $url = config('app.marketplace') . 'extensions/' . $id . '?version=' . config('app.version');
        $response = Http::get($url);
        if (!$response->successful()) {
            return redirect()->route('admin.extensions.browse')->with('error', 'Extension not found');
        }
        $extension = $response->json();
        $path = base_path('app/Extensions/' . ucfirst($extension['type']) . 's/' . $extension['name']);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
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

    public function download(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $rqname = $request->input('name');
        $name = explode('-', $rqname)[0];
        $type = explode('-', $rqname)[1];

        $extension = Extension::where('name', $name)->first();
        if (!$extension) {
            $extension = new Extension();
            $extension->name = $name;
            // Type to lowercase -s
            $extension->type = substr($type, 0, -1);
            $extension->enabled = 0;
            $extension->save();
        }
        $url = 'https://api.github.com/repos/Paymenter/Extensions/contents/' . $type . '/' . $name . '?ref=' . config('app.version');
        $response = Http::get($url);
        $response = json_decode($response);
        if (isset($response->message)) {
            return redirect()->route('admin.extensions')->with('error', 'Extension not found');
        }

        $path = base_path('app/Extensions/' . $type . '/' . $name);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        } else {
            if (!$request->get('verify')) {
                return redirect()->route('admin.extensions')->withInput()->with('verify', true);
            }
        }
        foreach ($response as $file) {
            if (strtolower($file->name) !== 'readme.md') {
                $path = base_path('app/Extensions/' . $type . '/' . $name . '/' . $file->name);
                $this->handleFile($file, $path);
            }
        }

        return redirect()->route('admin.extensions')->with('success', 'Extension downloaded successfully');
    }

    private function handleFile($file, $path)
    {
        if ($file->type == 'dir') {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $response = Http::get($file->url);
            $response = json_decode($response);

            foreach ($response as $file) {
                $this->handleFile($file, $path . '/' . $file->name);
            }
            return;
        }
        $filecontent = Http::get($file->download_url);
        $filecontent = $filecontent->body();
        file_put_contents($path, $filecontent);
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
        }

        return view('admin.extensions.edit', compact('extension'));
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
}
