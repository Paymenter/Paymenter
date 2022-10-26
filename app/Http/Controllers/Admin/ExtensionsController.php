<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Extensions;
use Illuminate\Http\Request;

class ExtensionsController extends Controller
{
    public function index()
    {
        $servers = $this->servers();
        $gateways = $this->gateways();
        return view('admin.extensions.index', compact('servers', 'gateways'));
    }

    public function edit($sort, $name){
        if($sort == 'server'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $name . '/extension.json')));
            $db = Extensions::where('name', $name)->first();
            $extension->enabled = $db->enabled;
            $extension->id = $db->id;
            $extension->type = 'server';
            return view('admin.extensions.edit', compact('extension'));
        }elseif($sort == 'gateway'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Gateways/' . $name . '/extension.json')));
            $db = Extensions::where('name', $name)->first();
            $extension->enabled = $db->enabled;
            $extension->id = $db->id;
            $extension->type = 'gateway';
            return view('admin.extensions.edit', compact('extension'));
        }
    }

    public function update(Request $request, $sort, $name){
        if($sort == 'server'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $name . '/extension.json')));
            foreach ($extension->config as $config) {
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            Extensions::where('name', $extension->name)->update(['enabled' => $request->input('enabled')]);
            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        }elseif($sort == 'gateway'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Gateways/' . $name . '/extension.json')));
            foreach($extension->config as $config){
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            Extensions::where('name', $extension->name)->update(['enabled' => $request->input('enabled')]);
            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        }
    }
    function servers()
    {
        $extensions = [];
        $folders = scandir(base_path('app/Extensions/Servers/'));
        foreach ($folders as $folder) {
            if ($folder != '.' && $folder != '..') {
                $extensions[$folder] = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $folder . '/extension.json')));
            }

        }
        return $extensions;
    }

    function gateways()
    {
        $extensions = [];
        $folders = scandir(base_path('app/Extensions/Gateways/'));
        foreach ($folders as $folder) {
            if ($folder != '.' && $folder != '..') {
                $extensions[$folder] = json_decode(file_get_contents(base_path('app/Extensions/Gateways/' . $folder . '/extension.json')));
            }
        }
        return $extensions;
    }
}
