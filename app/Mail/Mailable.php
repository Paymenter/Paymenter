<?php

namespace App\Mail;



use Spatie\MailTemplates\TemplateMailable;

class Mailable extends TemplateMailable
{
    /** @var string */
    public $company_name;

    /** @var string */
    public $css;

    /** @var string */
    public $logo;

    public function __construct()
    {
        $this->company_name = config('settings::app_name') ?? '';
        $this->css = config('settings::email_css') ?? '';
    }

    public function getHtmlLayout(): string
    {
        return file_get_contents(base_path('resources/views/emails/template.html'));
    }
}