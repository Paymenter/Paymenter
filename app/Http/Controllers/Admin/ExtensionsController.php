<?php

namespace App\Http\Controllers\Admin;

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

    public function edit($sort, $id){
        if($sort == 'server'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $id . '/extension.json')));
            return view('admin.extensions.edit', compact('extension'));
        }elseif($sort == 'gateway'){
            $extension = json_decode(file_get_contents(base_path('app/Extensions/Gateways/' . $id . '/extension.json')));
            return view('admin.extensions.edit', compact('extension'));
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
