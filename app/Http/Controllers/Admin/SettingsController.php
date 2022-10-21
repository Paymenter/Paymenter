<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Qirolab\Theme\Theme;
use App\Models\Settings;

class SettingsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth.admin');
    }

    function index()
    {
        $themes = array_diff(scandir(base_path('themes')), array('..', '.'));
        $currentTheme = Config::get('theme.active');
        $settings = Settings::first();
        return view('admin.settings.index', compact('themes', 'currentTheme', 'settings'));
    }

    function update(Request $request)
    {   

        $request->validate([
            'theme' => 'required',
            'recaptcha' => 'required',
            'recaptcha_site_key' => 'required',
            'recaptcha_secret_key' => 'required'
        ]);
        $theme = request('theme');
        $settings = Settings::first();
        $settings->theme = $theme;
        $settings->recaptcha = request('recaptcha') == 'on' ? 1 : 0;
        $settings->recaptcha_site_key = request('recaptcha_site_key');
        $settings->recaptcha_secret_key = request('recaptcha_secret_key');
        $settings->save();
        Theme::set($theme);
        return redirect('/admin/settings')->with('success', 'Settings updated successfully');
    }
}
