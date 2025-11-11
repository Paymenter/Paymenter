<?php

namespace App\Helpers;

use App\Classes\PDF;
use App\Mail\Mail;
use App\Models\EmailLog;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCancellation;
use App\Models\TicketMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail as FacadesMail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\Compilers\BladeCompiler;

class NotificationHelper
{
    /**
     * Send an email notification.
     */
    public static function sendEmailNotification(
        NotificationTemplate $notificationTemplate,
        array $data,
        User $user,
        array $attachments = []
    ): void {
        $mail = new Mail($notificationTemplate, $data);

        $emailLog = EmailLog::create([
            'user_id' => $user->id,
            'subject' => $mail->envelope()->subject,
            'to' => $user->email,
            'body' => $mail->render(),
        ]);

        // Add the email log id to the payload
        $mail->email_log_id = $emailLog->id;

        foreach ($attachments as $attachment) {
            $mail->attachFromStorage($attachment['path'], $attachment['name'], $attachment['options'] ?? []);
        }

        FacadesMail::to($user->email)
            ->bcc($notificationTemplate->bcc)
            ->cc($notificationTemplate->cc)
            ->queue($mail);
    }

    public static function sendSystemEmailNotification(
        string $subject,
        string $body,
        array $attachments = [],
        ?string $email = null,
    ): void {
        if (!$email) {
            $email = config('settings.system_email_address');
        }
        if (!$email || config('settings.mail_disable')) {
            return;
        }
        $mail = new \App\Mail\SystemMail([
            'subject' => $subject,
            'body' => $body,
        ]);
        $emailLog = EmailLog::create([
            'subject' => $mail->envelope()->subject,
            'to' => $email,
            'body' => $mail->render(),
        ]);

        // Add the email log id to the payload
        $mail->email_log_id = $emailLog->id;

        foreach ($attachments as $attachment) {
            $mail->attachFromStorage($attachment['path'], $attachment['name'], $attachment['options'] ?? []);
        }

        FacadesMail::to($email)
            ->queue($mail);
    }

    public static function sendInAppNotification(
        NotificationTemplate $notification,
        array $data,
        User $user,
        bool $show_in_app = true,
        bool $show_as_push = true
    ): void {
        Notification::create([
            'user_id' => $user->id,
            'title' => BladeCompiler::render($notification->in_app_title, $data),
            'body' => BladeCompiler::render($notification->in_app_body, $data),
            'url' => isset($notification->in_app_url) ? BladeCompiler::render($notification->in_app_url, $data) : null,
            'show_in_app' => $show_in_app,
            'show_as_push' => $show_as_push,
        ]);
    }

    public static function sendNotification(
        $notificationTemplateKey,
        array $data,
        User $user,
        array $attachments = [],
        bool $show_in_app = true,
        bool $show_as_push = true
    ): void {
        $notification = NotificationTemplate::where('key', $notificationTemplateKey)->first();
        if (!$notification || !$notification->enabled) {
            return;
        }

        $userPreference = $user->notificationsPreferences()->where('notification_template_id', $notification->id)->first();

        if ($notification->isEnabledForPreference($userPreference, 'mail') && !config('settings.mail_disable')) {
            self::sendEmailNotification($notification, $data, $user, $attachments);
        }

        if ($notification->isEnabledForPreference($userPreference, 'app')) {
            self::sendInAppNotification($notification, $data, $user, $show_in_app, $show_as_push);
        }
    }

    public static function loginDetectedNotification(User $user, array $data = []): void
    {
        self::sendNotification('new_login_detected', $data, $user);
    }

    public static function invoiceNotification(User $user, Invoice $invoice, $key = 'new_invoice_created'): void
    {
        $data = [
            'invoice' => $invoice,
            'items' => $invoice->items,
            'total' => $invoice->formattedTotal,
            'has_subscription' => $invoice->items->filter(fn ($item) => $item->reference_type === Service::class && $item->reference->subscription_id)->isNotEmpty(),
        ];

        // Generate the invoice PDF
        $pdf = PDF::generateInvoice($invoice);
        // Generate path
        if (!file_exists(storage_path('app/invoices'))) {
            // Create the directory if it doesn't exist
            mkdir(storage_path('app/invoices'), 0755, true);
        }
        // Save the PDF to a temporary location
        $pdfPath = storage_path('app/invoices/' . ($invoice->number ?? $invoice->id) . '.pdf');
        $pdf->save($pdfPath);

        // Attach the PDF to the email
        $attachments = [
            [
                'path' => 'invoices/' . ($invoice->number ?? $invoice->id) . '.pdf',
                'name' => 'invoice.pdf',
            ],
        ];

        self::sendNotification($key, $data, $user, $attachments);
    }

    public static function invoiceCreatedNotification(User $user, Invoice $invoice): void
    {
        self::invoiceNotification($user, $invoice, 'new_invoice_created');
    }

    public static function invoicePaidNotification(User $user, Invoice $invoice): void
    {
        self::invoiceNotification($user, $invoice, 'invoice_paid');
    }

    public static function invoicePaymentFailedNotification(User $user, Invoice $invoice): void
    {
        self::invoiceNotification($user, $invoice, 'invoice_payment_failed');
    }

    public static function orderCreatedNotification(User $user, Order $order, array $data = []): void
    {
        $data = [
            'order' => $order,
            'items' => $order->services,
            'total' => $order->formattedTotal,
        ];
        self::sendNotification('new_order_created', $data, $user);
    }

    public static function serverCreatedNotification(User $user, Service $service, array $data = []): void
    {
        $data['service'] = $service;
        self::sendNotification('new_server_created', $data, $user);
    }

    public static function serverSuspendedNotification(User $user, Service $service, array $data = []): void
    {
        $data['service'] = $service;
        self::sendNotification('server_suspended', $data, $user);
    }

    public static function serverTerminatedNotification(User $user, Service $service, array $data = []): void
    {
        $data['service'] = $service;
        self::sendNotification('server_terminated', $data, $user);
    }

    public static function ticketMessageNotification(User $user, TicketMessage $ticketMessage, array $data = []): void
    {
        $data['ticketMessage'] = $ticketMessage;
        self::sendNotification('new_ticket_message', $data, $user);
    }

    public static function emailVerificationNotification(User $user, array $data = []): void
    {
        $data['user'] = $user;
        $data['url'] = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->email),
            ]
        );
        self::sendNotification('email_verification', $data, $user);
    }

    public static function passwordResetNotification(User $user, array $data = []): void
    {
        $data['user'] = $user;
        self::sendNotification('password_reset', $data, $user);
    }

    public static function serviceCancellationReceivedNotification(User $user, ServiceCancellation $cancellation, array $data = []): void
    {
        $data['cancellation'] = $cancellation;
        $data['service'] = $cancellation->service;
        self::sendNotification('service_cancellation_received', $data, $user);
    }
}
