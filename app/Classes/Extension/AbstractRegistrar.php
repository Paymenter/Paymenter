<?php

namespace App\Classes\Extension;

use App\Exceptions\RegistrarException;
use App\Services\Domain\DomainService;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class AbstractRegistrar extends Registrar
{
    public function createServer($service, $settings = [], $properties = [])
    {
        $domain = DomainService::ensureForService($service);

        if (!$domain) {
            throw new RegistrarException('The service does not contain a domain name.');
        }

        try {
            $facts = $this->register($domain);
            DomainService::apply($domain, $facts, 'payment');

            return $facts;
        } catch (Throwable $e) {
            DomainService::markFailed($domain, $e->getMessage());

            throw $e;
        }
    }

    public function renewServer($service, $settings = [], $properties = [])
    {
        $domain = DomainService::ensureForService($service);

        if (!$domain) {
            throw new RegistrarException('The service does not contain a domain name.');
        }

        try {
            $facts = $this->renew($domain);
            DomainService::apply($domain, $facts, 'payment');

            return $facts;
        } catch (Throwable $e) {
            DomainService::markFailed($domain, $e->getMessage());

            throw $e;
        }
    }

    public function suspendServer($service, $settings = [], $properties = [])
    {
        return null;
    }

    public function terminateServer($service, $settings = [], $properties = [])
    {
        Log::warning('A domain service was not terminated automatically. Delete the domain from the admin panel instead.', [
            'service_id' => $service->id,
        ]);

        return false;
    }

    public function getActions($service, $settings = [], $properties = [])
    {
        return [];
    }
}
