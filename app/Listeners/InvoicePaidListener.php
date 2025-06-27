<?php

namespace App\Listeners;

use App\Events\Invoice\Paid;
use App\Jobs\Server\CreateJob;
use App\Jobs\Server\UnsuspendJob;
use App\Jobs\Server\UpgradeJob;
use App\Models\Credit;
use App\Models\Service;
use App\Models\ServiceUpgrade;

class InvoicePaidListener
{
    /**
     * Handle the event.
     */
    public function handle(Paid $event): void
    {
        // Update services if invoice is paid (suspended -> active etc.)
        $event->invoice->items->each(function ($item) {
            if ($item->reference_type == Service::class) {
                $service = $item->reference;
                if (!$service) {
                    return;
                }
                if ($service->product->server) {
                    if ($service->status == Service::STATUS_SUSPENDED) {
                        UnsuspendJob::dispatch($service);
                    } elseif ($service->status == Service::STATUS_PENDING) {
                        CreateJob::dispatch($service);
                    }
                }
                $service->status = Service::STATUS_ACTIVE;
                $service->expires_at = $service->calculateNextDueDate();
                $service->save();
            } elseif ($item->reference_type == ServiceUpgrade::class) {
                $serviceUpgrade = $item->reference;
                if (!$serviceUpgrade || $serviceUpgrade->status !== ServiceUpgrade::STATUS_PENDING) {
                    return;
                }
                $serviceUpgrade->status = ServiceUpgrade::STATUS_COMPLETED;
                $serviceUpgrade->save();

                $service = $serviceUpgrade->service;
                $service->plan_id = $serviceUpgrade->plan_id;
                $service->product_id = $serviceUpgrade->product_id;
                $service->save();

                foreach ($serviceUpgrade->configs as $config) {
                    $service->configs()->updateOrCreate(
                        ['config_option_id' => $config->config_option_id],
                        ['config_value_id' => $config->config_value_id]
                    );
                }
                $service->recalculatePrice();
                if ($service->product->server) {
                    UpgradeJob::dispatch($service);
                }
            } elseif ($item->reference_type == Credit::class) {
                // Check if user has credits in this currency
                $user = $item->invoice->user;
                $credit = $user->credits()->where('currency_code', $item->invoice->currency_code)->first();

                if ($credit) {
                    $credit->amount += $item->price;
                    $credit->save();
                } else {
                    $user->credits()->create([
                        'currency_code' => $item->invoice->currency_code,
                        'amount' => $item->price,
                    ]);
                }
            }
        });
    }
}
