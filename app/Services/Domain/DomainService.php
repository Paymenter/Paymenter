<?php

namespace App\Services\Domain;

use App\Classes\Extension\Registrar;
use App\Enums\DomainStatus;
use App\Events\Domain\Failed;
use App\Events\Domain\Transitioned;
use App\Exceptions\RegistrarException;
use App\Helpers\ExtensionHelper;
use App\Models\Domain;
use App\Models\Product;
use App\Models\Server;
use App\Models\Service;
use App\Models\Tld;
use App\Models\TldPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class DomainService
{
    private const FACT_KEYS = [
        'registrar_ref',
        'status',
        'registered_at',
        'expires_at',
        'grace_until',
        'redemption_until',
        'locked',
        'privacy',
        'nameservers',
        'transfer_status',
    ];

    public static function ensureForService(Service $service): ?Domain
    {
        if ($domain = $service->domain) {
            return $domain;
        }

        $registrar = self::registrarForService($service);
        if (!$registrar) {
            return null;
        }

        $data = $registrar->domainFromService($service);
        if (!$data || empty($data['name'])) {
            return null;
        }

        $name = self::normalizeName($data['name']);
        $serverId = $service->product?->server_id;
        $tldName = ($data['tld'] ?? null) ?: self::tldFromName($name, $serverId);
        $tld = $service->product
            ? self::findTldForProduct($tldName, $service->product)
            : self::findTld($tldName, $serverId);

        return Domain::firstOrCreate(
            ['service_id' => $service->id],
            [
                'server_id' => $serverId,
                'tld_id' => $tld?->id,
                'name' => $name,
                'tld' => $tldName,
                'registrar' => $service->product?->server?->extension,
                'status' => DomainStatus::Pending,
            ]
        );
    }

    public static function apply(Domain $domain, array $facts, string $reason = 'sync'): Domain
    {
        $status = self::validateFacts($facts);
        $previousStatus = $domain->status;

        $result = DB::transaction(function () use ($domain, $facts, $reason, $status) {
            $domain->fill(array_intersect_key($facts, array_flip(self::FACT_KEYS)));

            if ($status) {
                $domain->status = $status;
            }

            foreach (['registered_at', 'expires_at', 'grace_until', 'redemption_until'] as $date) {
                if (array_key_exists($date, $facts)) {
                    $domain->{$date} = $facts[$date] ? Carbon::parse($facts[$date]) : null;
                }
            }

            $domain->sync_error = null;

            if ($domain->isDirty()) {
                $domain->last_transition_reason = $reason;
                $domain->save();
            }

            $syncedAt = now();
            Domain::whereKey($domain->getKey())->update(['last_synced_at' => $syncedAt]);
            $domain->last_synced_at = $syncedAt;

            $service = $domain->service;
            if (!$service) {
                return $domain;
            }

            $isAdmin = $reason === 'admin' || preg_match('/^admin:\d+$/', $reason) === 1;

            if ($domain->status === DomainStatus::Active) {
                if ($service->status === Service::STATUS_PENDING) {
                    $service->status = Service::STATUS_ACTIVE;
                } elseif ($service->status === Service::STATUS_SUSPENDED && ($reason === 'payment' || $isAdmin)) {
                    $service->status = Service::STATUS_ACTIVE;
                }
            } elseif ($domain->status === DomainStatus::Cancelled) {
                $service->status = Service::STATUS_CANCELLED;
            }

            if ($domain->expires_at) {
                $service->expires_at = $domain->expires_at;
            }

            if ($service->isDirty()) {
                $service->save();
            }

            return $domain;
        });

        if ($status && $previousStatus !== $result->status) {
            event(new Transitioned($result, $previousStatus, $result->status, $reason));
        }

        return $result;
    }

    public static function markFailed(Domain $domain, string $error, string $operation = 'registration'): void
    {
        $previousStatus = $domain->status;

        $domain->update([
            'status' => DomainStatus::Failed,
            'sync_error' => mb_substr($error, 0, 2000),
            'last_transition_reason' => 'extension',
        ]);

        event(new Failed($domain, $error, $operation));

        if ($previousStatus !== $domain->status) {
            event(new Transitioned($domain, $previousStatus, $domain->status, 'extension'));
        }
    }

    public static function deleteAtRegistrar(Domain $domain, string $reason = 'admin'): bool
    {
        if ($domain->status !== DomainStatus::Redemption) {
            throw new RegistrarException('Only domains in redemption can be deleted at the registrar.');
        }

        $registrar = self::registrarForDomain($domain);
        if (!$registrar) {
            throw new RegistrarException('No registrar is available for this domain.');
        }

        if (!$registrar->delete($domain)) {
            return false;
        }

        self::apply($domain, ['status' => DomainStatus::Cancelled], $reason);

        return true;
    }

    public static function registrarForService(Service $service): ?Registrar
    {
        $server = $service->product?->server;

        return $server ? ExtensionHelper::getRegistrar($server) : null;
    }

    public static function registrarForDomain(Domain $domain): ?Registrar
    {
        return $domain->server ? ExtensionHelper::getRegistrar($domain->server) : null;
    }

    public static function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));

        if (function_exists('idn_to_ascii')) {
            $ascii = idn_to_ascii($name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($ascii !== false) {
                $name = $ascii;
            }
        }

        return rtrim($name, '.');
    }

    public static function tldFromName(string $name, ?int $serverId = null): ?string
    {
        $name = self::normalizeName($name);

        $tld = Tld::query()
            ->when($serverId, fn ($query) => $query->where(
                fn ($query) => $query->where('server_id', $serverId)->orWhereNull('server_id')
            ))
            ->pluck('tld')
            ->sortByDesc(fn (string $tld) => strlen($tld))
            ->first(fn (string $tld) => str_ends_with($name, '.' . strtolower($tld)));

        return $tld ? strtolower($tld) : null;
    }

    public static function syncTldsForServer(Server $server): int
    {
        $registrar = ExtensionHelper::getRegistrar($server);
        if (!$registrar) {
            return 0;
        }

        try {
            $tlds = $registrar->getTlds();
        } catch (Throwable) {
            return 0;
        }

        $count = 0;

        foreach ($tlds as $tld => $data) {
            if (!is_array($data)) {
                continue;
            }

            $name = strtolower(ltrim((string) $tld, '.'));
            $currency = $data['currency'] ?? config('settings.default_currency');

            if (!$name || !$currency) {
                continue;
            }

            $model = Tld::firstOrCreate(
                ['tld' => $name, 'server_id' => $server->id],
                ['enabled' => true]
            );

            TldPrice::updateOrCreate(
                [
                    'tld_id' => $model->id,
                    'currency_code' => $currency,
                    'years' => 1,
                ],
                ['cost' => $data['cost'] ?? null]
            );

            $count++;
        }

        return $count;
    }

    public static function backfillForServer(Server $server): int
    {
        $count = 0;

        Service::query()
            ->whereHas('product', fn ($query) => $query->where('server_id', $server->id))
            ->whereDoesntHave('domain')
            ->eachById(function (Service $service) use ($server, &$count) {
                Domain::withoutEvents(function () use ($service, $server, &$count) {
                    $domain = self::ensureForService($service);

                    if (!$domain) {
                        return;
                    }

                    $tldName = $domain->tld ?: self::tldFromName($domain->name, $server->id);
                    $tld = self::findTldForProduct($tldName, $service->product);

                    $status = match ($service->status) {
                        Service::STATUS_CANCELLED => DomainStatus::Cancelled,
                        Service::STATUS_PENDING => DomainStatus::Pending,
                        default => DomainStatus::Active,
                    };

                    $domain->update([
                        'server_id' => $server->id,
                        'tld_id' => $tld?->id,
                        'tld' => $tldName,
                        'registrar' => $server->extension,
                        'registrar_ref' => $domain->registrar_ref ?: $domain->name,
                        'status' => $status,
                        'registered_at' => $domain->registered_at ?: $service->created_at,
                        'expires_at' => $domain->expires_at ?: $service->expires_at,
                        'last_transition_reason' => 'backfill',
                    ]);

                    $count++;
                });
            });

        return $count;
    }

    private static function validateFacts(array $facts): ?DomainStatus
    {
        $status = null;

        if (array_key_exists('status', $facts)) {
            $status = $facts['status'] instanceof DomainStatus
                ? $facts['status']
                : DomainStatus::tryFrom((string) $facts['status']);

            if (!$status) {
                throw new RegistrarException('The registrar returned an unknown domain status.');
            }
        }

        foreach (['registered_at', 'expires_at', 'grace_until', 'redemption_until'] as $date) {
            if (array_key_exists($date, $facts) && $facts[$date] !== null) {
                try {
                    Carbon::parse($facts[$date]);
                } catch (Throwable) {
                    throw new RegistrarException('The registrar returned an invalid ' . $date . '.');
                }
            }
        }

        if (array_key_exists('nameservers', $facts) && $facts['nameservers'] !== null && !is_array($facts['nameservers'])) {
            throw new RegistrarException('The registrar returned invalid nameservers.');
        }

        foreach (['locked', 'privacy'] as $boolean) {
            if (array_key_exists($boolean, $facts) && $facts[$boolean] !== null && !is_bool($facts[$boolean])) {
                throw new RegistrarException('The registrar returned an invalid ' . $boolean . ' value.');
            }
        }

        foreach (['registrar_ref', 'transfer_status'] as $string) {
            if (array_key_exists($string, $facts) && $facts[$string] !== null && !is_string($facts[$string])) {
                throw new RegistrarException('The registrar returned an invalid ' . $string . ' value.');
            }
        }

        return $status;
    }

    public static function findTld(?string $tldName, ?int $serverId): ?Tld
    {
        if (!$tldName) {
            return null;
        }

        if ($serverId) {
            $tld = Tld::where('tld', $tldName)->where('server_id', $serverId)->first();
            if ($tld) {
                return $tld;
            }
        }

        return Tld::where('tld', $tldName)->whereNull('server_id')->first();
    }

    public static function findTldForProduct(?string $tldName, ?Product $product): ?Tld
    {
        if (!$tldName || !$product) {
            return null;
        }

        return Tld::where('tld', $tldName)
            ->where(function ($query) use ($product) {
                $query->where('product_id', $product->id)
                    ->orWhere(fn ($query) => $query->whereNull('product_id')->where('server_id', $product->server_id));
            })
            ->orderByRaw('CASE WHEN product_id = ? THEN 0 ELSE 1 END', [$product->id])
            ->first();
    }
}
