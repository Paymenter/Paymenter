<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Mail\Mailable as TemplateMailable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;

class Mailable extends TemplateMailable
{
    public function build()
    {
        $emailTemplate = EmailTemplate::where('mailable', get_class($this))->first();
        if (!$emailTemplate) {
            return $this;
        }
        $html_template = $emailTemplate->html_template;
        View::addNamespace('mail', base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views/html'));
        $html_template = BladeCompiler::render($html_template, $this->buildViewData());
        $html_template = \Illuminate\Mail\Markdown::parse($html_template);

        $emailTemplate->subject = BladeCompiler::render($emailTemplate->subject, $this->buildViewData());

        return $this->view('emails.base', ['content' => $html_template])
            ->subject($emailTemplate->subject);
    }
}
