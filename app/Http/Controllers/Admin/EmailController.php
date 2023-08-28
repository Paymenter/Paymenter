<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
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
}
