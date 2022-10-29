<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Orders;
use App\Helpers\ExtensionHelper;

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
            } else if ($order->status == 'pending') {
                $order->status = 'cancelled';
                $order->save();
            } else if ($order->status == 'suspended') {
                // Check if expiry_date is 7 days before now with strtotime
                if (strtotime($order->expiry_date) < strtotime('-1 week')) {
                    ExtensionHelper::terminateServer($order);
                    $order->status = 'cancelled';
                    $order->save();
                }
            }
        }
        $this->info('Cron Job Finished');
        return Command::SUCCESS;
    }
}
