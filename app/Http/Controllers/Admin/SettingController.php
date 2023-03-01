<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Qirolab\Theme\Theme;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class SettingController extends Controller
{
    public function index()
    {
        $tabs = [];
        // Get current theme
        foreach (glob(Theme::getViewPaths()[1] . '/admin/settings/settings/*.blade.php') as $filename) {
            $tabs[] = 'admin.settings.settings.' . basename($filename, '.blade.php');
        }
        $themes = array_diff(scandir(base_path('themes')), ['..', '.']);

        return view('admin.settings.index', [
            'tabs' => $tabs,
            'themes' => $themes,
        ]);
    }

    public function general(Request $request)
    {
        $request->validate([
            'app_name' => 'required|max:255',
            'seo_title' => 'required|max:255',
            'seo_description' => 'required|max:255',
            'seo_twitter_card' => 'boolean',
            'app_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'currency' => 'required|max:10',
            'currency_sign' => 'required|max:4',
        ]);
        if ($request->hasFile('app_logo')) {
            $imageName = time() . '.' . $request->app_logo->extension();
            $request->app_logo->move(public_path('images'), $imageName);
            $path = '/images/' . $imageName;
            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }

        $theme = request('theme');
        try {
            $theme = Theme::set($theme);
        } catch (\Exception $e) {
            $theme = 'default';
        }
        foreach ($request->except(['_token', 'app_logo', 'app_favicon']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect('/admin/settings#general')->with('success', 'Settings updated successfully');
    }

    public function email(Request $request)
    {
        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required|in:tls,ssl,none',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ]);
        if ($request->mail_encryption == 'none') {
            $request->merge(['mail_encryption' => null]);
        }
        // Loop through all settings
        foreach ($request->except(['_token']) as $key => $value) {
            if ($key == 'mail_password') {
                $value = Crypt::encryptString($value);
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect('/admin/settings#mail')->with('success', 'Settings updated successfully');
    }

    public function testEmail(Request $request)
    {
        config(['mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => $request->mail_host,
            'port' => $request->mail_port,
            'encryption' => $request->mail_encryption,
            'username' => $request->mail_username,
            'password' => $request->mail_password ? $request->mail_password : config('mail.password', ''),
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

    public function login(Request $request)
    {
        Setting::updateOrCreate(['key' => 'discord_client_id'], ['value' => $request->discord_client_id]);
        Setting::updateOrCreate(['key' => 'discord_client_secret'], ['value' => $request->discord_client_secret]);
        Setting::updateOrCreate(['key' => 'discord_enabled'], ['value' => $request->discord_enabled]);

        return redirect('/admin/settings#login')->with('success', 'Settings updated successfully');
    }

    public function security(Request $request)
    {
        Setting::updateOrCreate(['key' => 'recaptcha_site_key'], ['value' => $request->recaptcha_site_key]);
        Setting::updateOrCreate(['key' => 'recaptcha_secret_key'], ['value' => $request->recaptcha_secret_key]);
        Setting::updateOrCreate(['key' => 'recaptcha'], ['value' => $request->recaptcha]);

        return redirect('/admin/settings#security')->with('success', 'Settings updated successfully');
    }
}
