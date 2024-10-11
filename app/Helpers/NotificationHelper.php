<?php

namespace App\Helpers;

use App\Classes\MailAttachment;
use App\Mail\Mail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail as FacadesMail;

class NotificationHelper
{
    /**
     * Send an email notification.
     */
    public static function sendEmailNotification(
        $emailTemplateKey,
        array $data,
        User $user,
        array $attachments = []
    ): void {
        $emailTemplate = EmailTemplate::where('key', $emailTemplateKey)->first();
        if (!$emailTemplate || !$emailTemplate->enabled) {
            return;
        }
        $mail = new Mail($emailTemplate, $data);

        $emailLog = EmailLog::create([
            'user_id' => $user->id,
            'subject' => $mail->envelope()->subject,
            'to' => $user->email,
            'body' => $mail->render(),
        ]);

        // Add the email log id to the payload
        $mail->email_log_id = $emailLog->id;

        foreach($attachments as $attachment) {
            $mail->attachFromStorage($attachment['path'], $attachment['name'], $attachment['options'] ?? []);
        }

        FacadesMail::to($user->email)
            ->bcc($emailTemplate->bcc)
            ->cc($emailTemplate->cc)
            ->send($mail);
    }

    public static function newLoginDetectedNotification(User $user, array $data = []): void
    {
        self::sendEmailNotification('new_login_detected', $data, $user);
    }

    public static function newInvoiceCreatedNotification(User $user, Invoice $invoice): void
    {
        $data = [
            'invoice' => $invoice,
            'items' => $invoice->items,
            'total' => $invoice->formattedTotal,
            'has_subscription' => $invoice->items->filter(fn ($item) => $item->relation_type === Service::class && $item->relation->subscription_id)->isNotEmpty(),
        ];
        $attachments = [
            [
                'path' => 'invoices/' . $invoice->id . '.pdf',
                'name' => 'invoice.pdf',
            ]
        ];
        self::sendEmailNotification('new_invoice_created', $data, $user, $attachments);
    }

    public static function newServerCreatedNotification(User $user, Service $service, array $data = []): void
    {
        $data['service'] = $service;
        self::sendEmailNotification('new_server_created', $data, $user);
    }

    public static function serverSuspendedNotification(User $user, Service $service, array $data = []): void
    {
        $data['service'] = $service;
        self::sendEmailNotification('server_suspended', $data, $user);
    }

    public static function serverTerminatedNotification(User $user, Service $service, array $data = []): void
    {
        $data['service'] = $service;
        self::sendEmailNotification('server_terminated', $data, $user);
    }

    public static function newTicketMessageNotification(User $user, TicketMessage $ticketMessage, array $data = []): void
    {
        $data['ticketMessage'] = $ticketMessage;
        self::sendEmailNotification('new_ticket_message', $data, $user);
    }
}
