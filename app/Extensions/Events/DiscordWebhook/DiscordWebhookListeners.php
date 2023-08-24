<?php

namespace App\Extensions\Events\DiscordWebhook;

use App\Events\Invoice\InvoiceCreated;
use App\Events\Invoice\InvoicePaid;
use App\Events\Ticket\TicketCreated;
use App\Events\Ticket\TicketMessageCreated;
use App\Events\User\UserCreated;
use App\Helpers\ExtensionHelper;

class DiscordWebhookListeners
{
    private function sendWebhook(string $title, string $message, array $fields = [], string $color = '00ff00'): void
    {
        $data = [
            'embeds' => [
                [
                    'title' =>  $title,
                    'description' => $message,
                    'color' => hexdec($color),
                    'fields' => $fields,
                ],
            ],
        ];
        $url = ExtensionHelper::getConfig('DiscordWebhook', 'webhook_url');
        if (!$url) {
            return;
        }
        if (ExtensionHelper::getConfig('DiscordWebhook', 'ping_type') == 'user') {
            $data['content'] = '<@' . ExtensionHelper::getConfig('DiscordWebhook', 'ping_id') . '>';
        } else if (ExtensionHelper::getConfig('DiscordWebhook', 'ping_type') == 'role') {
            $data['content'] = '<@&' . ExtensionHelper::getConfig('DiscordWebhook', 'ping_id') . '>';
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curl);
        curl_close($curl);
    }

    /**
     * Handle the Invoice created event.
     */
    public function handleInvoiceCreated(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;
        $message = " ";
        $fields = [
            [
                'name' => 'Invoice ID',
                'value' => '[#' . $invoice->id . '](' . route('admin.invoices.show', $invoice->id) . ')',
                'inline' => true,
            ],
            [
                'name' => 'Invoice total',
                'value' => config('settings::currency_sign') . $invoice->total(),
                'inline' => true,
            ],
            [
                'name' => 'Invoice status',
                'value' => $invoice->status,
                'inline' => true,
            ],
            [
                'name' => 'Invoice user',
                'value' => '[' . $invoice->user->name . '](' . route('admin.clients.edit', $invoice->user->id) . ')',
                'inline' => true,
            ],
        ];
        $this->sendWebhook('New invoice created', $message, $fields);
    }

    /**
     * Handle the Invoice paid event.
     */
    public function handleInvoicePaid(InvoicePaid $event): void
    {
        $invoice = $event->invoice;
        $message = "Invoice paid: {$invoice->id} - {$invoice->user->name} -  " . config('settings::currency_sign') . "{$invoice->total()}";
        $this->sendWebhook('Invoice paid', $message);
    }


    public function newTicketMessage($event)
    {
        $ticket = $event->ticket;
        $message = $event->message;
        $dcmessage = "{$message->message}\n";
        $fields = [
            [
                'name' => 'Ticket ID',
                'value' => '[#' . $ticket->id . '](' . route('admin.tickets.show', $ticket->id) . ')',
                'inline' => true,
            ],
            [
                'name' => 'Ticket status',
                'value' => $ticket->status,
                'inline' => true,
            ],
            [
                'name' => 'Ticket priority',
                'value' => $ticket->priority,
                'inline' => true,
            ],
            [
                'name' => 'Ticket subject',
                'value' => $ticket->title,
                'inline' => true,
            ],
            [
                'name' => 'Ticket user',
                'value' => '[' . $message->user->name . '](' . route('admin.clients.edit', $message->user->id) . ')',
                'inline' => true,
            ],
        ];
        $this->sendWebhook('New ticket message', $dcmessage, $fields);
    }

    public function newUser($event)
    {
        $user = $event->user;
        $message = " ";
        $fields = [
            [
                'name' => 'User ID',
                'value' => '[#' . $user->id . '](' . route('admin.clients.edit', $user->id) . ')',
                'inline' => true,
            ],
            [
                'name' => 'User name',
                'value' => $user->name,
                'inline' => true,
            ],
            [
                'name' => 'User email',
                'value' => $user->email,
                'inline' => true,
            ],
        ];
        $this->sendWebhook('New user', $message, $fields);
    }

    public function newTicket($event)
    {
        $ticket = $event->ticket;
        $message = " ";
        $fields = [
            [
                'name' => 'Ticket ID',
                'value' => '[#' . $ticket->id . '](' . route('admin.tickets.show', $ticket->id) . ')',
                'inline' => true,
            ],
            [
                'name' => 'Ticket status',
                'value' => $ticket->status,
                'inline' => true,
            ],
            [
                'name' => 'Ticket priority',
                'value' => $ticket->priority,
                'inline' => true,
            ],
            [
                'name' => 'Ticket subject',
                'value' => $ticket->title,
                'inline' => true,
            ],
            [
                'name' => 'Ticket user',
                'value' => '[' . $ticket->user->name . '](' . route('admin.clients.edit', $ticket->user->id) . ')',
                'inline' => true,
            ],
        ];
        $this->sendWebhook('New ticket', $message, $fields);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(): array
    {
        return [
            InvoiceCreated::class => 'handleInvoiceCreated',
            InvoicePaid::class => 'handleInvoicePaid',
            TicketMessageCreated::class => 'newTicketMessage',
            UserCreated::class => 'newUser',
            TicketCreated::class => 'newTicket',
        ];
    }
}
