<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!EmailTemplate::where('mailable', \App\Mail\Test::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/test.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Test::class,
                'subject' => 'Test Mail',
                'html_template' => $html,
            ]);
        }
        if (!EmailTemplate::where('mailable', \App\Mail\Invoices\NewInvoice::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/invoices/new.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Invoices\NewInvoice::class,
                'subject' => 'New invoice',
                'html_template' => $html,
            ]);
        }
        if (!EmailTemplate::where('mailable', \App\Mail\Invoices\UnpaidInvoice::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/invoices/new.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Invoices\UnpaidInvoice::class,
                'subject' => 'Unpaid invoice reminder',
                'html_template' => $html,
            ]);
        }
        if (!EmailTemplate::where('mailable', \App\Mail\Orders\DeletedOrder::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/orders/deleted.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Orders\DeletedOrder::class,
                'subject' => 'Deleted order due to non-payment',
                'html_template' => $html,
            ]);
        }
        if (!EmailTemplate::where('mailable', \App\Mail\Orders\NewOrder::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/orders/new.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Orders\NewOrder::class,
                'subject' => 'New order',
                'html_template' => $html,
            ]);
        }
        if (!EmailTemplate::where('mailable', \App\Mail\Tickets\NewTicket::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/tickets/new.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Tickets\NewTicket::class,
                'subject' => 'New ticket',
                'html_template' => $html,
            ]);
        }
        if (!EmailTemplate::where('mailable', \App\Mail\Tickets\NewTicketMessage::class)->exists()) {
            $html = file_get_contents(__DIR__ . '/EmailTemplates/tickets/new-message.blade.php');

            EmailTemplate::create([
                'mailable' => \App\Mail\Tickets\NewTicketMessage::class,
                'subject' => 'New ticket message',
                'html_template' => $html,
            ]);
        }
    }
}
