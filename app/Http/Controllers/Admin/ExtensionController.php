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

    public function download(Request $request){
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
            $extension->type = $type == 'Servers' ? 'server' : 'gateway';
            $extension->enabled = 0;
            $extension->save();
        }
        $url = 'https://api.github.com/repos/Paymenter/Extensions/contents/'.$type.'/'.$name;
        $response = Http::get($url);
        $response = json_decode($response);
        if(isset($response->message)){
            return redirect()->route('admin.extensions')->with('error', 'Extension not found');
        }
        
        $path = base_path('app/Extensions/'.$type.'/'.$name);
        if(!file_exists($path)){
            mkdir($path, 0777, true);
        } else {
            if(!$request->get('verify')){
                return redirect()->route('admin.extensions')->withInput()->with('verify', true);
            }
        }

        foreach($response as $file){
            if(strtolower($file->name) !== 'readme.md'){
                $fileurl = $file->download_url;
                $filecontent = Http::get($fileurl);
                $filecontent = $filecontent->body();
                $path = base_path('app/Extensions/'.$type.'/'.$name.'/'.$file->name);
                file_put_contents($path, $filecontent);
            }
        }
        return redirect()->route('admin.extensions')->with('success', 'Extension downloaded successfully');
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
            Extension::where('name', $extension->name)->update(['enabled' => $request->input('enabled'), 'display_name' => $request->input('display_name')]);

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
            Extension::where('name', $extension->name)->update(['enabled' => $request->input('enabled'), 'display_name' => $request->input('display_name')]);

            return redirect()->route('admin.extensions.edit', ['sort' => $sort, 'name' => $name])->with('success', 'Extension updated successfully');
        }
    }
}
