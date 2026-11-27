<?php

namespace App\Classes\Extension;

use App\Exceptions\RegistrarUnsupportedException;
use App\Models\Domain;
use App\Models\Service;

abstract class Registrar extends Server
{
    abstract public function getTlds(): array;

    abstract public function checkAvailability(string $name): array;

    abstract public function register(Domain $domain): array;

    abstract public function renew(Domain $domain): array;

    abstract public function getInfo(Domain $domain): array;

    public function domainFromService(Service $service): ?array
    {
        $properties = $service->properties->pluck('value', 'key');

        $name = $properties->get('domain') ?? $properties->get('name');
        if (!$name) {
            return null;
        }

        return [
            'name' => $name,
            'tld' => $properties->get('tld'),
        ];
    }

    public function transferIn(Domain $domain, string $epp): array
    {
        throw new RegistrarUnsupportedException('This registrar does not support transfers.');
    }

    public function price(string $name, string $operation, int $years, string $currency): ?array
    {
        return null;
    }

    public function supports(string $capability): bool
    {
        return false;
    }

    public function delete(Domain $domain): bool
    {
        throw new RegistrarUnsupportedException('This registrar does not support deleting domains.');
    }

    public function getEppCode(Domain $domain): ?string
    {
        throw new RegistrarUnsupportedException('This registrar does not support EPP codes.');
    }

    public function setNameservers(Domain $domain, array $nameservers): bool
    {
        throw new RegistrarUnsupportedException('This registrar does not support changing nameservers.');
    }

    public function setLock(Domain $domain, bool $locked): bool
    {
        throw new RegistrarUnsupportedException('This registrar does not support changing the domain lock.');
    }

    public function setPrivacy(Domain $domain, bool $enabled): bool
    {
        throw new RegistrarUnsupportedException('This registrar does not support changing privacy.');
    }

    public function getDnsRecords(Domain $domain): array
    {
        throw new RegistrarUnsupportedException('This registrar does not support DNS records.');
    }
}
