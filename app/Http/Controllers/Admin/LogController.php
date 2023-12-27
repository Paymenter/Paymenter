<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $level = $request->get('level', 'error');
        $logs = \App\Models\Log::where('type', $level)->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.logs.index', compact('logs', 'level'));
    }

    public function debug(Request $request)
    {
        if($request->get('disable') == 'true') {
            Setting::updateOrCreate(['key' => 'debug_logs_enabled'], ['value' => 'false']);
        } else {
            Setting::updateOrCreate(['key' => 'debug_logs_enabled'], ['value' => 'true']);
        }

        return redirect()->back();
    }
}
