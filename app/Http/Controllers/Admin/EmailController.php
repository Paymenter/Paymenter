<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Testing\Fakes\MailFake;
use Illuminate\View\Compilers\BladeCompiler;

class EmailController extends Controller
{
    /**
     * Get all sended emails
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        $emails = EmailLog::with('user')->orderByDesc('created_at')->paginate(config('app.pagination'));
        return view('admin.emails.index', compact('emails'));
    }

    /**
     * Get all templates
     * 
     * @return \Illuminate\View\View
     */
    public function templates(): \Illuminate\View\View
    {
        $templates = EmailTemplate::all();
        return view('admin.emails.templates.index', compact('templates'));
    }

    /**
     * Get template
     * 
     * @param EmailTemplate $template
     * @return \Illuminate\View\View
     */
    public function template(EmailTemplate $template): \Illuminate\View\View
    {
        View::addNamespace('mail', base_path('vendor/laravel/framework/src/Illuminate/Mail/resources/views/html'));
        $html_template = \Illuminate\Mail\Markdown::parse($template->html_template);
        $html_template = view('emails.base', ['content' => $html_template])->render();

        return view('admin.emails.templates.template', compact('template', 'html_template'));
    }

    /**
     * Update template
     * 
     * @param EmailTemplate $template
     * @return \Illuminate\View\View
     */
    public function update(EmailTemplate $template, Request $request)
    {
        $template->update($request->only('subject', 'html_template', 'text_template'));
        return redirect()->route('admin.email.templates')->with('success', 'Template updated');
    }
}
