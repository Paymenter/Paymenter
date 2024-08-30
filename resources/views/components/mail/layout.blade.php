{!! Illuminate\View\Compilers\BladeCompiler::render(config('settings.mail_header', null)) !!}
{!! Str::markdown($slot) !!}
{!! Illuminate\View\Compilers\BladeCompiler::render(config('settings.mail_footer', null)) !!}
