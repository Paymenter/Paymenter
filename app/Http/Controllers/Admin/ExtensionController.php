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

        return view('admin.extensions.edit', compact('extension'));
    }

    public function update(Request $request, $sort, $name)
    {
        if ($sort != 'server' && $sort != 'gateway' && $sort != 'event') {
            return redirect()->route('admin.extensions')->with('error', 'Extension not found');
        }
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
        $namespace = 'App\Extensions\\' . ucfirst($sort) . 's\\' . $name . '\\' . $name;
        $extension->config = json_decode(json_encode((new $namespace($extension))->getConfig()));
        $extension->name = $name;
        foreach ($extension->config as $config) {
            if ($config->required && !$request->input($config->name)) {
                return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('error', 'Please fill in all required fields');
            }
            ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
        }
        Extension::where('name', $extension->name)->update(['enabled' => $request->input('enabled'), 'display_name' => $request->input('display_name')]);

        return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
    }
}
