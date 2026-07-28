<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale');

        if (! is_string($locale) || $locale === '') {
            $preferred = Auth::user()?->preferred_language;
            if (is_string($preferred) && $preferred !== '') {
                $locale = $preferred;
            }
        }

        if (! is_string($locale) || $locale === '') {
            $locale = (string) config('app.locale');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
