<?php

namespace App\Listeners;

use App\Events\Invoice\Paid;
use App\Models\Credit;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Services\Service\RenewServiceService;
use App\Services\ServiceUpgrade\ServiceUpgradeService;

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
                if (!$service || !($service instanceof Service)) {
                    return;
                }
                (new RenewServiceService)->handle($service);
            } elseif ($item->reference_type == ServiceUpgrade::class) {
                $serviceUpgrade = $item->reference;
                if (!$serviceUpgrade || $serviceUpgrade->status !== ServiceUpgrade::STATUS_PENDING || !($serviceUpgrade instanceof ServiceUpgrade)) {
                    return;
                }

                // Handle the upgrade
                (new ServiceUpgradeService)->handle($serviceUpgrade);
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
