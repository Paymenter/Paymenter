<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Mail\Invoices\NewInvoice;
use App\Models\OrderProduct;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:cronjob';

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
            if ($order->price == 0.00) {
                continue;
            }
            if ($order->status == 'paid') {
                $order->status = 'suspended';
                $order->save();
                ExtensionHelper::suspendServer($order);
                $invoice = $order->lastInvoice();
                // Free products don't have invoices
                if ($invoice) {
                    NotificationHelper::sendUnpaidInvoiceNotification($invoice, $order->order->user);
                }
                $this->info('Suspended server: ' . $order->id);
            } elseif ($order->status == 'suspended' || $order->status == 'pending') {
                if (strtotime($order->expiry_date) < strtotime('-' . config('settings::remove_unpaid_order_after', 7) . ' days')) {
                    ExtensionHelper::terminateServer($order);
                    $order->status = 'cancelled';
                    NotificationHelper::sendDeletedOrderNotification($order->order, $order->order->user);
                    $order->save();
                    $invoice = $order->lastInvoice();

                    if ($invoice) {
                        if ($invoice->status !== 'paid') {
                            $invoice->status = 'cancelled';
                            $invoice->cancelled_at = now()->format('Y-m-d H:i:s');
                            $invoice->save();
                            $this->info('Invoice ' . $invoice->id . ' status changed to ' . $invoice->status);
                        }
                    }
                }
            }
        }
        $orders = OrderProduct::where('expiry_date', '<', now()->addDays(7))->where('status', '!=', 'cancelled')->get();
        $invoiceProcessed = 0;
        foreach ($orders as $order) {
            if ($order->billing_cycle == 'free' || $order->billing_cycle == 'one-time' || $order->price == 0.00) {
                continue;
            }
            // FIXME: Why do we need to call it twice?
            $order->getOpenInvoices();

            // Get all InvoiceItems for this product
            if ($order->getOpenInvoices()->count() > 0) {
                continue;
            }

            $invoice = new \App\Models\Invoice();
            $invoice->order_id = $order->order->id;
            $invoice->status = 'pending';
            $invoice->user_id = $order->order->user_id;
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
            $invoiceItem->description = $order->product()->get()->first() ? $order->product()->get()->first()->name . ' ' . $description : '' . $description;
            $invoiceItem->total = $order->price;
            $invoiceItem->save();

            NotificationHelper::sendNewInvoiceNotification($invoice, $order->order->user);

            if ($invoice->total() == 0) {
                ExtensionHelper::paymentDone($invoice->id);
                $this->info('Invoice ' . $invoice->id . ' status changed to ' . $invoice->status);
            }
            $invoiceProcessed++;
            $this->info('Sended Invoice: ' . $invoice->id);
        }
        $this->info('Sended Number of Invoices: ' . $invoiceProcessed);
        $this->info('Cron Job Finished');
        Setting::updateOrCreate(['key' => 'cronjob_last_run'], ['value' => now()]);

        return Command::SUCCESS;
    }
}
