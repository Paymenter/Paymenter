<?php

namespace App\Http\Controllers\Admin;

use App\Models\Extension;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;

class ExtensionController extends Controller
{
    public function index()
    {
        $servers = scandir(base_path('app/Extensions/Servers/'));
        $gateways = scandir(base_path('app/Extensions/Gateways/'));
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

        return view('admin.extensions.index', compact('servers', 'gateways'));
    }

    public function edit($sort, $name)
    {
        if ($sort == 'server') {
            include_once base_path('app/Extensions/Servers/' . $name . '/index.php');
            $extension = new \stdClass();
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;
            $db = Extension::where('name', $name)->first();
            if (!$db) {
                Extension::create([
                    'name' => $name,
                    'enabled' => false,
                    'type' => 'server',
                ]);
                $db = Extension::where('name', $name)->first();
            }
            $extension->enabled = $db->enabled;
            $extension->id = $db->id;
            $extension->type = 'server';

            return view('admin.extensions.edit', compact('extension'));
        } elseif ($sort == 'gateway') {
            include_once base_path('app/Extensions/Gateways/' . $name . '/index.php');
            $extension = new \stdClass();
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;
            $db = Extension::where('name', $name)->first();
            if (!$db) {
                Extension::create([
                    'name' => $name,
                    'enabled' => false,
                    'type' => 'gateway',
                ]);
                $db = Extension::where('name', $name)->first();
            }
            $extension->enabled = $db->enabled;
            $extension->id = $db->id;
            $extension->type = 'gateway';

            return view('admin.extensions.edit', compact('extension'));
        }
    }

    public function update(Request $request, $sort, $name)
    {
        if ($sort == 'server') {
            include_once base_path('app/Extensions/Servers/' . $name . '/index.php');
            $extension = new \stdClass();
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;
            foreach ($extension->config as $config) {
                if ($config->required && !$request->input($config->name)) {
                    return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('error', 'Please fill in all required fields');
                }
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            Extension::where('name', $extension->name)->update(['enabled' => $request->input('enabled')]);

            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        } elseif ($sort == 'gateway') {
            include_once base_path('app/Extensions/Gateways/' . $name . '/index.php');
            $extension = new \stdClass();
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;
            foreach ($extension->config as $config) {
                if ($config->required && !$request->input($config->name)) {
                    return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('error', 'Please fill in all required fields');
                }
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            Extension::where('name', $extension->name)->update(['enabled' => $request->input('enabled')]);

            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        }
    }
}
