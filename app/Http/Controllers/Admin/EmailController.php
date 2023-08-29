<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\Setting;
use Illuminate\Http\Request;

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

    public function templates(): \Illuminate\View\View
    {
        return view('admin.emails.templates');
    }

    public function templatesUpdate(Request $request) {

        $request->validate([
            'template_name' => 'required',
            'template' => 'required|min:1',
        ]);
        $template_name = $request->template_name;
        $transformed_name = str_replace('_', ' ', $template_name);
        Setting::updateOrCreate(['key' => $request->template_name.'_email_template'], ['value' => $request->template]);
        return redirect()->route('admin.email.templates')->with('success', ucfirst($transformed_name).' email template updated successfully!')->with('updatedTemplate', $template_name);
    }
}
