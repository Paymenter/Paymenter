<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Qirolab\Theme\Theme;
use App\Models\Settings;

class SettingsController extends Controller
{
    function index()
    {
        error_log(config('settings::sidebar'));
        $tabs = [];
        // Get current theme     
        error_log(print_r(Theme::getViewPaths()[0], true)); 
        foreach (glob(Theme::getViewPaths()[0] . '/admin/settings/settings/*.blade.php') as $filename) {
            error_log(print_r($filename, true));
            $tabs[] = 'admin.settings.settings.' . basename($filename, '.blade.php');
        }

        return view('admin.settings.index', [
            'tabs' => $tabs
        ]);
        /*
        $themes = array_diff(scandir(base_path('themes')), array('..', '.'));
        $currentTheme = Config::get('theme.active');
        $settings = Settings::first();
        return view('admin.settings.index2', compact('themes', 'currentTheme', 'settings'));*/
    }

    function update(Request $request)
    {   

        $request->validate([
            'theme' => 'required',
            'currency_sign' => 'required',
        ]);
        $request->merge([
            'recaptcha' => $request->recaptcha == 'on' ? 1 : 0,
            'seo_twitter_card' => $request->seo_twitter_card == 'on' ? 1 : 0,
            'sidebar' => $request->navbar
        ]);

        $theme = request('theme');
        $settings = Settings::first();
        $settings->update($request->all());
        Theme::set($theme);
        return redirect('/admin/settings')->with('success', 'Settings updated successfully');
    }
}
