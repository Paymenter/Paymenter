<?php

namespace App\Services\ServiceUpgrade;

use App\Jobs\Server\UpgradeJob;
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
        });
    }
}
