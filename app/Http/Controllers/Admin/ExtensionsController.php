<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Extensions;
use Illuminate\Http\Request;
use stdClass;

class ExtensionsController extends Controller
{
    public function index()
    {
        $servers = scandir(base_path('app/Extensions/Servers/'));
        $gateways = scandir(base_path('app/Extensions/Gateways/'));
        foreach ($servers as $key => $server) {
            if ($server == '.' || $server == '..') continue;
            // Check if the extension is enabled
            $extension = Extensions::where('name', $server)->first();
            if(!$extension) {
                $extension = new Extensions();
                $extension->name = $server;
                $extension->type = 'server';
                $extension->enabled = 0;
                $extension->save();
            }
        }
        foreach ($gateways as $key => $gateway) {
            if ($gateway == '.' || $gateway == '..') continue;
            // Check if the extension is enabled
            $extension = Extensions::where('name', $gateway)->first();
            if(!$extension) {
                $extension = new Extensions();
                $extension->name = $gateway;
                $extension->type = 'gateway';
                $extension->enabled = 0;
                $extension->save();
            }
        }
        return view('admin.extensions.index', compact('servers', 'gateways'));
    }

    public function edit($sort, $name){
        if($sort == 'server'){
            include_once base_path('app/Extensions/Servers/' . $name . '/index.php');
            $extension = new stdClass;
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;
            $db = Extensions::where('name', $name)->first();
            if(!$db){
                Extensions::create([
                    'name' => $name,
                    'enabled' => false,
                    'type' => 'server'
                ]);
                $db = Extensions::where('name', $name)->first();
            }
            $extension->enabled = $db->enabled;
            $extension->id = $db->id;
            $extension->type = 'server';
            return view('admin.extensions.edit', compact('extension'));
        }elseif($sort == 'gateway'){
            include_once base_path('app/Extensions/Gateways/' . $name . '/index.php');
            $extension = new stdClass;
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;            
            $db = Extensions::where('name', $name)->first();
            if(!$db){
                Extensions::create([
                    'name' => $name,
                    'enabled' => false,
                    'type' => 'gateway'
                ]);
                $db = Extensions::where('name', $name)->first();
            }
            $extension->enabled = $db->enabled;
            $extension->id = $db->id;
            $extension->type = 'gateway';
            return view('admin.extensions.edit', compact('extension'));
        }
    }

    public function update(Request $request, $sort, $name){
        if($sort == 'server'){
            include_once base_path('app/Extensions/Servers/' . $name . '/index.php');
            $extension = new stdClass;
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;
            foreach ($extension->config as $config) {
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            Extensions::where('name', $extension->name)->update(['enabled' => $request->input('enabled')]);
            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        }elseif($sort == 'gateway'){
            include_once base_path('app/Extensions/Gateways/' . $name . '/index.php');
            $extension = new stdClass;
            $function = $name . '_getConfig';
            $extension2 = json_decode(json_encode($function()));
            $extension->config = $extension2;
            $extension->name = $name;            
            foreach($extension->config as $config){
                ExtensionHelper::setConfig($extension->name, $config->name, $request->input($config->name));
            }
            Extensions::where('name', $extension->name)->update(['enabled' => $request->input('enabled')]);
            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        }
    }
}
