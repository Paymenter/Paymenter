<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExtensionsController extends Controller
{
    public function index()
    {
        $servers = $this->servers();
        return view('admin.extensions.index', compact('servers'));
    }

    public function edit($id){
        $extension = json_decode(file_get_contents(base_path('app/Extensions/Servers/' . $id . '/extension.json')));
        return view('admin.extensions.edit', compact('extension'));
    }

    function servers()
    {
        $extensions = [];
        $folders = scandir(base_path('app/Extensions/Servers/'));
        // For each folder in app/Extensions
        foreach ($folders as $folder) {
            // If folder is not . or ..
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
        // For each folder in app/Extensions
        foreach ($folders as $folder) {
            // If folder is not . or ..
            if ($folder != '.' && $folder != '..') {
                $extensions[] = $folder;
                // Read config and add to array
                $content = json_encode(file_get_contents(base_path('app/Extensions/Gateways/' . $folder . '/extension.json')));
                error_log(print_r($content, true));
            }
        }
        return $extensions;
    }
}
