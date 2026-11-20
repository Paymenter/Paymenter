<?php

namespace App\Services\Domain;

use App\Enums\DomainStatus;
use App\Models\Domain;
use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DomainRenewalService
{
    public static function createInvoice(Domain $domain): ?Invoice
    {
        return DB::transaction(function () use ($domain) {
            $domain = Domain::whereKey($domain->getKey())->lockForUpdate()->first();

            if (!$domain || $domain->status === DomainStatus::Cancelled) {
                return null;
            }

            $service = Service::whereKey($domain->service_id)->lockForUpdate()->first();

            if (!$service) {
                return null;
            }

            $domain->setRelation('service', $service);

            $existing = $service->invoices()->where('status', 'pending')->first();
            if ($existing) {
                return $existing;
            }

            $price = DomainPricing::forRenewal($domain);
            if (!$price->available) {
                return null;
            }

            $invoice = $service->invoices()->make([
                'user_id' => $service->user_id,
                'status' => 'pending',
                'due_at' => $service->expires_at ?? now(),
                'currency_code' => $service->currency_code,
            ]);
            $invoice->save();

            $invoice->items()->create([
                'reference_id' => $service->id,
                'reference_type' => $service::class,
                'price' => $price->price,
                'quantity' => 1,
                'description' => $service->description,
            ]);

            return $invoice;
        });
    }
}
