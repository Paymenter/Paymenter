<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Qirolab\Theme\Theme;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    function index()
    {
        error_log(config('settings::sidebar'));
        $tabs = [];
        // Get current theme     
        foreach (glob(Theme::getViewPaths()[0] . '/admin/settings/settings/*.blade.php') as $filename) {
            $tabs[] = 'admin.settings.settings.' . basename($filename, '.blade.php');
        }
        $themes = array_diff(scandir(base_path('themes')), array('..', '.'));

        return view('admin.settings.index', [
            'tabs' => $tabs,
            'themes' => $themes,
        ]);
    }

    function general(Request $request)
    {
        $request->validate([
            'app_name' => 'required|max:255',
            'seo_title' => 'required|max:255',
            'seo_description' => 'required|max:255',
            'seo_keywords' => 'required|max:255',
            'seo_twitter_card' => 'boolean',
            'app_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('app_logo')) {
            $imageName = time() . '.' . $request->app_logo->extension();
            $request->app_logo->move(public_path('images'), $imageName);
            $path = '/images/' . $imageName;
            Settings::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }

        $theme = request('theme');
        Theme::set($theme);
        try{
            $theme = Theme::set($theme);
        }catch(\Exception $e){
            $theme = 'default';
        }
        foreach ($request->except(['_token', 'app_logo', 'app_favicon']) as $key => $value) {
            Settings::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect('/admin/settings#general')->with('success', 'Settings updated successfully');
    }

    function email(Request $request)
    {
        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ]);
        // Loop through all settings
        foreach ($request->except(['_token']) as $key => $value) {
            Settings::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect('/admin/settings#mail')->with('success', 'Settings updated successfully');
    }

    function testEmail(Request $request)
    {
        config(['mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => $request->mail_host,
            'port' => $request->mail_port,
            'encryption' => $request->mail_encryption,
            'username' => $request->mail_username,
            'password' => $request->mail_password,
            'timeout' => null,
            'auth_mode' => null,
        ]]);
        config(['mail.from.address' => $request->mail_from_address]);
        config(['mail.from.name' => $request->mail_from_name]);

        $email = Auth::user()->email;
        try {
            Mail::raw('If you read this, your email is working! 🎊', function ($message) use ($email) {
                $message->to($email);
                $message->subject('Test Email');
                $message->from(config('mail.username'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            // Return json response
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Email sent successfully'], 200);
    }

    function login(Request $request)
    {
        Settings::updateOrCreate(['key' => 'discord_client_id'], ['value' => $request->discord_client_id]);
        Settings::updateOrCreate(['key' => 'discord_client_secret'], ['value' => $request->discord_client_secret]);
        Settings::updateOrCreate(['key' => 'discord_enabled'], ['value' => $request->discord_enabled]);

        return redirect('/admin/settings#login')->with('success', 'Settings updated successfully');
    }

    function security(Request $request)
    {
        Settings::updateOrCreate(['key' => 'recaptcha_site_key'], ['value' => $request->recaptcha_site_key]);
        Settings::updateOrCreate(['key' => 'recaptcha_secret_key'], ['value' => $request->recaptcha_secret_key]);
        Settings::updateOrCreate(['key' => 'recaptcha'], ['value' => $request->recaptcha]);

        return redirect('/admin/settings#security')->with('success', 'Settings updated successfully');
    }
}
