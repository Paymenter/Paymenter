{!! Illuminate\View\Compilers\BladeCompiler::render(config('settings.mail_header', null)) !!}
{{ Illuminate\Mail\Markdown::parse($slot) }}
{!! Illuminate\View\Compilers\BladeCompiler::render(config('settings.mail_footer', null)) !!}

