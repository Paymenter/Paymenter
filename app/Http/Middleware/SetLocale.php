<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale', config('settings::language'));
            if (!config('settings::allow_auto_lang')) {
                if ($locale !== config('settings::language')) {
                    $locale = config('settings::language');
                    Session::put('locale', $locale);
                }
            }
        } else {
            if (!config('settings::allow_auto_lang')) {
                $locale = config('settings::language');
                Session::put('locale', $locale);
            } else {
                $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
                $languages = array_diff(scandir(base_path('lang')), ['..', '.']);
                foreach ($languages as $key => $language) {
                    if (strpos($language, '.json') !== false) {
                        unset($languages[$key]);
                    }
                }
                if (!in_array($locale, $languages)) {
                    $locale = config('settings::language');
                }
                Session::put('locale', $locale);
            }
        }
        App::setLocale($locale);

        return $next($request);
    }
}
