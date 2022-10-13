<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
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
            return view('admin.extensions.edit', compact('extension'));
        }elseif($sort == 'gateway'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Gateways/' . $name . '/extension.json')));
            return view('admin.extensions.edit', compact('extension'));
        }
    }

    public function update(Request $request, $sort, $name){
        if($sort == 'server'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $name . '/extension.json')));
            foreach ($extension->config as $config) {
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            return redirect()->route('admin.extensions');
        }elseif($sort == 'gateway'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Gateways/' . $name . '/extension.json')));
            foreach($extension->config as $config){
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            return redirect()->route('admin.extensions');
        }
    }
    function servers()
    {
        $extensions = [];
        $folders = scandir(base_path('app/Extensions/Servers/'));
        foreach ($folders as $folder) {
            if ($folder != '.' && $folder != '..') {
                $extensions[$folder] = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $folder . '/extension.json')));
                error_log($extensions[$folder]->name);
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
                error_log($extensions[$folder]->name);
            }
        }
        return $extensions;
    }
}
