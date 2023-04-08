<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Helpers\ExtensionHelper;
use App\Mail\Invoices\NewInvoice;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Cron Job';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Cron Job Started');
        $orders = OrderProduct::where('expiry_date', '<', now())->get();
        foreach ($orders as $order) {
            if ($order->status == 'paid') {
                $order->status = 'suspended';
                $order->save();
                ExtensionHelper::suspendServer($order);
            } elseif ($order->status == 'pending') {
                $order->status = 'cancelled';
                $order->save();
            } elseif ($order->status == 'suspended') {
                if (strtotime($order->expiry_date) < strtotime('-1 week')) {
                    ExtensionHelper::terminateServer($order);
                    $order->status = 'cancelled';
                    $order->save();
                }
            }
        }
        $orders = OrderProduct::where('expiry_date', '<', now()->addDays(7))->where('status', '!=', 'cancelled')->get();
        $invoiceProcessed = 0;
        foreach ($orders as $order) {
            if($order->billing_cycle == 'free' || $order->billing_cycle == 'one-time') {
                continue;
            }
            // Get all InvoiceItems for this product
            $invoiceItems = $order->invoices()->get();
            // Check if there is a pending invoice
            foreach ($invoiceItems as $invoiceItem) {
                $invoice = $invoiceItem->invoice()->get()->first();
                if ($invoice->status == 'pending') {
                    // Stop processing this order
                    continue 2;
                }
            }

            $invoice = new \App\Models\Invoice();
            $invoice->order_id = $order->id;
            $invoice->status = 'pending';
            $invoice->user_id = $order->order()->get()->first()->client;
            $invoice->save();
            $date;
            if ($order->billing_cycle == 'monthly') {
                $date = date('Y-m-d', strtotime('+1 month', strtotime($order->expiry_date)));
            } elseif ($order->billing_cycle == 'quarterly') {
                $date = date('Y-m-d', strtotime('+3 month', strtotime($order->expiry_date)));
            } elseif ($order->billing_cycle == 'semi_annually') {
                $date = date('Y-m-d', strtotime('+6 month', strtotime($order->expiry_date)));
            } elseif ($order->billing_cycle == 'annually') {
                $date = date('Y-m-d', strtotime('+1 year', strtotime($order->expiry_date)));
            } elseif ($order->billing_cycle == 'biennially') {
                $date = date('Y-m-d', strtotime('+2 year', strtotime($order->expiry_date)));
            } elseif ($order->billing_cycle == 'triennially') {
                $date = date('Y-m-d', strtotime('+3 year', strtotime($order->expiry_date)));
            } else {
                $date = date('Y-m-d', strtotime('+1 month', strtotime($order->expiry_date)));
                $order->billing_cycle = 'monthly';
                $order->save();
            }
            // Add Invoice Items
            $invoiceItem = new \App\Models\InvoiceItem();
            $invoiceItem->invoice_id = $invoice->id;
            $invoiceItem->product_id = $order->id;
            $description = $order->billing_cycle ? '(' . date('Y-m-d', strtotime($order->expiry_date)) . ' - ' . date('Y-m-d', strtotime($date)) . ')' : '';
            $invoiceItem->description = $order->product()->get()->first() ? $order->product()->get()->first()->name . $description : '' . $description;
            $invoiceItem->total = $order->price;
            $invoiceItem->save();

            if (!config('settings::mail_disabled')) {
                try {
                    Mail::to($order->order()->get()->first()->client()->get())->send(new NewInvoice($invoice));
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
            $invoiceProcessed++;
        }
        $this->info('Sended Number of Invoices: ' . $invoiceProcessed);
        $this->info('Cron Job Finished');

        return Command::SUCCESS;
    }
}
