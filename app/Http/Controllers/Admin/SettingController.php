<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationHelper;
use App\Models\Setting;
use Qirolab\Theme\Theme;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;

class SettingController extends Controller
{
    public function index()
    {
        $tabs = [];
        // Use default theme
        Theme::set('default');
        // Get current theme
        foreach (glob(Theme::getViewPaths()[0] . '/admin/settings/settings/*.blade.php') as $filename) {
            $tabs[] = 'admin.settings.settings.' . basename($filename, '.blade.php');
        }
        unset($tabs[array_search('admin.settings.settings.general', $tabs)]);
        array_unshift($tabs, 'admin.settings.settings.general');
        $themes = array_diff(scandir(base_path('themes')), ['..', '.']);
        $languages = array_diff(scandir(base_path('lang')), ['..', '.']);
        foreach ($languages as $key => $language) {
            if (strpos($language, '.json') !== false) {
                unset($languages[$key]);
            }
        }
        $themeConfig = new \stdClass();
        if (file_exists(base_path('themes/' . config('settings::theme-active') . '/theme.json'))) {
            $themeConfig = json_decode(file_get_contents(base_path('themes/' . config('settings::theme-active') . '/theme.json')));
            if (!$themeConfig) {
                $themeConfig = new \stdClass();
            }
        }
        if (!isset($themeConfig->settings)) {
            $themeConfig->settings = [];
        }
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
        return view('admin.settings.index', [
            'tabs' => $tabs,
            'themes' => $themes,
            'languages' => $languages,
            'themeConfig' => $themeConfig,
            'timezones' => $timezones
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
            'currency_position' => 'required|in:left,right',
            'language' => 'required',
            'allow_auto_lang' => 'boolean',
            'timezone' => 'required',
            'remove_unpaid_order_after' => 'required|numeric|min:0',
        ]);
        if ($request->hasFile('app_logo')) {
            $imageName = time() . '.' . $request->app_logo->extension();
            $request->app_logo->move(public_path('images'), $imageName);
            $path = '/images/' . $imageName;
            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }
        foreach ($request->except(['_token', 'app_logo', 'app_favicon']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        // Needs to manually do this because otherwise it isn't sended
        if (!$request->get('allow_auto_lang')) {
            Setting::updateOrCreate(['key' => 'allow_auto_lang'], ['value' => 0]);
        }
        if (!$request->get('seo_twitter_card')) {
            Setting::updateOrCreate(['key' => 'seo_twitter_card'], ['value' => 0]);
        }

        return redirect('/admin/settings#general')->with('success', 'Settings updated successfully');
    }

    public function company(Request $request)
    {
        $request->validate([
            'company_name' => 'max:255',
            'company_address' => 'max:255',
            'company_city' => 'max:255',
            'company_country' => 'max:255',
            'company_zip' => 'max:255',
            'company_phone' => 'max:255',
            'company_email' => 'max:255|email',
            'company_vat' => 'max:255',
        ]);
        foreach ($request->except(['_token', 'app_logo', 'app_favicon']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect('/admin/settings#company')->with('success', 'Settings updated successfully');
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
        if ($request->mail_encryption == 'none') $request->merge(['mail_encryption' => null]);
        // Loop through all settings
        foreach ($request->except(['_token']) as $key => $value) {
            if ($key == 'mail_password') {
                $value = Crypt::encryptString($value);
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        if (!$request->get('mail_disabled')) {
            Setting::updateOrCreate(['key' => 'mail_disabled'], ['value' => 0]);
        }
        if (!$request->get('must_verify_email')) {
            Setting::updateOrCreate(['key' => 'must_verify_email'], ['value' => null]);
        } else {
            $user = auth()->user();
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();   
                $user->save();
            }
        }
        Artisan::call('queue:restart');

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
            'password' => $request->mail_password ? $request->mail_password : Crypt::decrypt(config('mail.password', '')),
        ]]);
        config(['mail.from.address' => $request->mail_from_address]);
        config(['mail.from.name' => $request->mail_from_name]);

        try {
            NotificationHelper::sendTestNotification(auth()->user());
        } catch (\Exception $e) {
            // Return json response
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Email sent successfully'], 200);
    }

    public function login(Request $request)
    {
        Setting::updateOrCreate(['key' => 'discord_enabled'], ['value' => $request->discord_enabled]);
        Setting::updateOrCreate(['key' => 'google_enabled'], ['value' => $request->google_enabled]);
        Setting::updateOrCreate(['key' => 'github_enabled'], ['value' => $request->github_enabled]);
        foreach ($request->except(['_token']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect('/admin/settings#login')->with('success', 'Settings updated successfully');
    }

    public function security(Request $request)
    {
        Setting::updateOrCreate(['key' => 'recaptcha'], ['value' => $request->recaptcha]);
        if ($request->get('tos') !== config('settings::tos_text')) {
            Setting::updateOrCreate(['key' => 'tos_last_updated'], ['value' => now()]);
        }
        Setting::updateOrCreate(['key' => 'tos'], ['value' => $request->tos]);

        foreach ($request->except(['_token']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect('/admin/settings#security')->with('success', 'Settings updated successfully');
    }

    public function theme(Request $request)
    {
        if($request->has('reset')) {
            foreach(Setting::where('key', 'like', 'theme:%')->get() as $setting) {
                $setting->delete();
            }
            return redirect('/admin/settings#theme')->with('success', 'Settings updated successfully');
        }
        $request->validate([
            'theme' => 'required',
        ]);
        Setting::updateOrCreate(['key' => 'theme'], ['value' => $request->theme]);
        foreach ($request->except(['_token', 'theme']) as $key => $value) {
            Setting::updateOrCreate(['key' => 'theme:' . $key], ['value' => $value]);
        }

        return redirect('/admin/settings#theme')->with('success', 'Settings updated successfully');
    }

    public function credits(Request $request)
    {
        $request->validate([
            'minimum_deposit' => 'required|numeric|min:0',
            'maximum_deposit' => 'required|numeric|min:0',
            'maximum_balance' => 'required|numeric|min:0',
        ]);
        Setting::updateOrCreate(['key' => 'credits'], ['value' => $request->credits]);
        foreach ($request->except(['_token']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return redirect('/admin/settings#credits')->with('success', 'Settings updated successfully');
    }

    public function affiliate(Request $request)
    {
        $request->validate([
            'affiliate' => 'sometimes|boolean',
            'affiliate_percentage' => 'required|numeric|min:0|max:100',
            'affiliate_type' => 'required|in:random,fixed,custom'
        ]);

        foreach ($request->except(['_token']) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Setting::updateOrCreate(['key' => 'affiliate'], ['value' => $request->affiliate ? 1 : 0]);

        return redirect('/admin/settings#affiliate')->with('success', 'Settings updated successfully');
    }
}
