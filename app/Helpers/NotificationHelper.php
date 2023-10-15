<?php

namespace App\Helpers;

use App\Mail\Invoices\NewInvoice;
use App\Mail\Invoices\UnpaidInvoice;
use App\Mail\Orders\DeletedOrder;
use App\Mail\Orders\NewOrder;
use App\Mail\Test;
use App\Mail\Tickets\NewTicket;
use App\Mail\Tickets\NewTicketMessage;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationHelper
{
    public static function bcc()
    {
        if (config('settings::bcc') == null) return [];
        return explode(',', config('settings::bcc')) ?? [];
    }

    protected static function sendMail($user, Mailable $mail)
    {
        if (config('settings::mail_disabled')) return;
        $emailLog = EmailLog::create([
            'user_id' => $user->id,
            'body' => $mail->render(),
            'subject' => $mail->subject,
            'body_text' => $mail->textView,
        ]);
        try {
            Mail::to($user->email)->bcc(self::bcc())->queue($mail);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $emailLog->update([
                'errors' => (string) $e,
                'success' => false,
            ]);
        }
    }

    /**
     * @param $order \App\Models\Order
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendNewOrderNotification($order, $user)
    {
        self::sendMail($user, new NewOrder($order));
    }

    /**
     * @param $invoice \App\Models\Invoice
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendNewInvoiceNotification($invoice, $user)
    {
        self::sendMail($user, new NewInvoice($invoice));
    }

    /**
     * @param $invoice \App\Models\Invoice
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendUnpaidInvoiceNotification($invoice, $user)
    {
        self::sendMail($user, new UnpaidInvoice($invoice));
    }

    /**
     * @param $invoice \App\Models\Invoice
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendDeletedOrderNotification($order, $user)
    {
        self::sendMail($user, new DeletedOrder($order));
    }

    /**
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendTestNotification($user)
    {
        self::sendMail($user, new Test($user));
    }

    /**
     * @param $ticket \App\Models\Ticket
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendNewTicketNotification($ticket, $user)
    {
        self::sendMail($user, new NewTicket($ticket));
    }

    /**
     * @param $ticket \App\Models\Ticket
     * @param $user \App\Models\User
     *
     * @return void
     */
    public static function sendNewTicketMessageNotification($ticket, $user)
    {
        self::sendMail($user, new NewTicketMessage($ticket));
    }
}
