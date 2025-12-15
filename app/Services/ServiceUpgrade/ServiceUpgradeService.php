<?php

namespace App\Services\ServiceUpgrade;

use App\Jobs\Server\UpgradeJob;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use Illuminate\Support\Facades\DB;

class ServiceUpgradeService
{
    /**
     * Handle the uploaded extension file.
     * The added file is always a zip file.
     *
     * @return void
     */
    public function handle(ServiceUpgrade $serviceUpgrade)
    {
        return DB::transaction(function () use ($serviceUpgrade) {
            $serviceUpgrade->status = ServiceUpgrade::STATUS_COMPLETED;
            $serviceUpgrade->save();

            // Check if old product stock should be increased
            $service = $serviceUpgrade->service;
            if ($service->product->stock !== null) {
                $serviceUpgrade->service->product->increment('stock', $serviceUpgrade->service->quantity);
            }

            $service->plan_id = $serviceUpgrade->plan_id;
            $service->product_id = $serviceUpgrade->product_id;
            $service->save();

            $service->refresh();

            // Decrease stock of new product if applicable
            if ($service->product->stock !== null) {
                $service->product->decrement('stock', $service->quantity);
            }

            // Update service configurations - remove old configs and add new ones
            $newConfigOptionIds = $serviceUpgrade->configs->pluck('config_option_id')->toArray();

            // Delete configs that are no longer applicable
            $service->configs()
                ->whereNotIn('config_option_id', $newConfigOptionIds)
                ->delete();

            // Update or create new configs
            foreach ($serviceUpgrade->configs as $config) {
                $service->configs()->updateOrCreate(
                    ['config_option_id' => $config->config_option_id],
                    ['config_value_id' => $config->config_value_id]
                );
            }

            $service->refresh();

            $service->price = $service->calculatePrice();
            $service->save();

            // Is there a pending renewal invoice? Update it.
            $pendingInvoice = $service->invoices()
                ->where('status', 'pending')
                ->first();

            if ($pendingInvoice) {
                $item = $pendingInvoice->items()
                    ->where('reference_type', Service::class)
                    ->where('reference_id', $service->id)
                    ->first();
                if ($item) {
                    $item->price = $service->price;
                    $item->save();
                }
            }

            if ($service->product->server) {
                UpgradeJob::dispatch($service);
            }
        });
    }
}
