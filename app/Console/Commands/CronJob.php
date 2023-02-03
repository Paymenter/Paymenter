<?php

namespace App\Console\Commands;

use App\Models\Orders;
use Illuminate\Console\Command;
use App\Helpers\ExtensionHelper;
use App\Mail\Invoices\NewInvoice;
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
        $orders = Orders::where('expiry_date', '<', now())->get();
        foreach ($orders as $order) {
            if ($order->status == 'paid') {
                $order->status = 'suspended';
                $order->save();
                ExtensionHelper::suspendServer($order);
            } elseif ($order->status == 'pending') {
                $order->status = 'cancelled';
                $order->save();
            } elseif ($order->status == 'suspended') {
                // Check if expiry_date is 7 days before now with strtotime
                if (strtotime($order->expiry_date) < strtotime('-1 week')) {
                    ExtensionHelper::terminateServer($order);
                    $order->status = 'cancelled';
                    $order->save();
                }
            }
        }
        $orders = Orders::where('expiry_date', '<', now()->addDays(7))->get();
        foreach ($orders as $order) {
            // Check if there is a pending invoice
            if ($order->invoices()->where('status', 'pending')->count() == 0) {
                $invoice = new \App\Models\Invoices();
                $invoice->order_id = $order->id;
                $invoice->status = 'pending';
                $invoice->user_id = $order->client;
                $invoice->save();
                if (!config('settings::mail_disabled')) {
                    try {
                        Mail::to($order->client()->get())->send(new NewInvoice($invoice));
                    } catch (\Exception $e) {
                        error_log($e->getMessage());
                    }
                }
            }
        }
        $this->info('Cron Job Finished');

        return Command::SUCCESS;
    }
}
