<?php
namespace App\Helpers\Mail;

use App\Models\{Invoices, Orders, Settings};
use Illuminate\Support\Facades\Mail;

class Invoice
{
    public function send(Invoices $invoices)
    {
        $user = $invoices->user()->first();

        $data = [
            'invoice' => $invoices
        ];
        Mail::send('emails.invoice', $data, function ($message) use ($invoices, $user) {
            $message->from(config('mail.from.address'), config('mail.from.name'));
            $message->to($user->email);
            $message->subject('Invoice #' . $invoices->id);
        });
    }
}
