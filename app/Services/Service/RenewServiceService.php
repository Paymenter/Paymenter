<?php

namespace App\Services\Service;

use App\Helpers\ExtensionHelper;
use App\Jobs\Server\CreateJob;
use App\Jobs\Server\UnsuspendJob;
use App\Models\Service;
use Throwable;

class RenewServiceService
{
    /**
     * Handle the service renewal.
     *
     * @return void
     */
    public function handle(Service $service)
    {
        $server = $service->product?->server;

        if ($server && ExtensionHelper::isRegistrar($server)) {
            if ($service->status === Service::STATUS_PENDING) {
                CreateJob::dispatch($service);
                $service->refresh();

                if (!$service->expires_at) {
                    $years = $service->domain?->years()
                        ?? (int) ($service->properties->firstWhere('key', 'years')->value ?? $service->quantity ?? 1);
                    $service->expires_at = now()->addYears(max(1, $years));
                    $service->save();
                }
            } else {
                try {
                    ExtensionHelper::renewServer($service);
                } catch (Throwable $e) {
                    report($e);
                }

                $service->refresh();
            }

            return;
        }

        if ($server) {
            if ($service->status == Service::STATUS_SUSPENDED) {
                UnsuspendJob::dispatch($service);
            } elseif ($service->status == Service::STATUS_PENDING) {
                CreateJob::dispatch($service);
            }
        }

        $service->expires_at = $service->calculateNextDueDate();
        $service->status = Service::STATUS_ACTIVE;
        $service->save();
    }
}
