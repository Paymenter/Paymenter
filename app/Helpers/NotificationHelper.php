<?php

namespace App\Helpers;

use App\Mail\Invoices\NewInvoice;
use App\Mail\Orders\NewOrder;
use App\Mail\Test;
use App\Mail\Tickets\NewTicket;
use App\Mail\Tickets\NewTicketMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotificationHelper
{
    public static function bcc()
    {
        if (config('settings::bcc') == null) return [];
        return explode(',', config('settings::bcc')) ?? [];
    }

    /**
     * @param $order \App\Models\Order
     * @param $user \App\Models\User
     * 
     * @return void
     */
    public static function sendNewOrderNotification($order, $user)
    {
        if (config('settings::mail_disabled')) return;
        Mail::to($user->email)->bcc(self::bcc())->queue(new NewOrder($order));
    }

    /**
     * @param $invoice \App\Models\Invoice
     * @param $user \App\Models\User
     * 
     * @return void
     */
    public static function sendNewInvoiceNotification($invoice, $user)
    {
        if (config('settings::mail_disabled')) return;
        Mail::to($user->email)->bcc(self::bcc())->queue(new NewInvoice($invoice));
    }

    /**
     * @param $user \App\Models\User
     * 
     * @return void
     */
    public static function sendTestNotification($user)
    {
        if (config('settings::mail_disabled')) return;
        Mail::to($user->email)->bcc(self::bcc())->send(new Test($user));
    }

    /**
     * @param $ticket \App\Models\Ticket
     * @param $user \App\Models\User
     * 
     * @return void
     */
    public static function sendNewTicketNotification($ticket, $user)
    {
        if (config('settings::mail_disabled')) return;
        Mail::to($user->email)->bcc(self::bcc())->queue(new NewTicket($ticket));
    }

    /**
     * @param $ticket \App\Models\Ticket
     * @param $user \App\Models\User
     * 
     * @return void
     */
    public static function sendNewTicketMessageNotification($ticket, $user)
    {
        if (config('settings::mail_disabled')) return;
        Mail::to($user->email)->bcc(self::bcc())->queue(new NewTicketMessage($ticket));
    }
}
