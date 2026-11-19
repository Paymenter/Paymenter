<?php

namespace Paymenter\Extensions\Servers\FakeRegistrar;

use App\Classes\Extension\AbstractRegistrar;
use App\Models\Domain;

class FakeRegistrar extends AbstractRegistrar
{
    public static int $constructed = 0;

    public static int $renewCalls = 0;

    public static int $registerCalls = 0;

    public static int $deleteCalls = 0;

    public static ?string $premiumName = null;

    public static ?array $premiumPrice = null;

    public static bool $throwOnPrice = false;

    public static bool $throwOnRenew = false;

    public static bool $throwOnCheck = false;

    public static bool $available = true;

    public function __construct($config = [])
    {
        parent::__construct($config);
        self::$constructed++;
    }

    public static function reset(): void
    {
        self::$constructed = 0;
        self::$renewCalls = 0;
        self::$registerCalls = 0;
        self::$deleteCalls = 0;
        self::$premiumName = null;
        self::$premiumPrice = null;
        self::$throwOnPrice = false;
        self::$throwOnRenew = false;
        self::$throwOnCheck = false;
        self::$available = true;
    }

    public function getTlds(): array
    {
        return ['com' => ['cost' => 9.50, 'currency' => 'USD']];
    }

    public function checkAvailability(string $name): array
    {
        if (self::$throwOnCheck) {
            throw new \RuntimeException('Availability check failed');
        }

        return ['available' => self::$available, 'premium' => $name === self::$premiumName];
    }

    public function price(string $name, string $operation, int $years, string $currency): ?array
    {
        if (self::$throwOnPrice) {
            throw new \RuntimeException('Price lookup failed');
        }

        return $name === self::$premiumName ? self::$premiumPrice : null;
    }

    public function delete(Domain $domain): bool
    {
        self::$deleteCalls++;

        return true;
    }

    public function register(Domain $domain): array
    {
        self::$registerCalls++;

        return [
            'registrar_ref' => 'fake-' . $domain->name,
            'status' => 'active',
            'registered_at' => now()->toDateString(),
            'expires_at' => now()->addYears($domain->years())->toDateString(),
        ];
    }

    public function renew(Domain $domain): array
    {
        self::$renewCalls++;

        if (self::$throwOnRenew) {
            throw new \RuntimeException('Renewal failed');
        }

        return [
            'status' => 'active',
            'expires_at' => $domain->expires_at->copy()->addYears($domain->years())->toDateString(),
        ];
    }

    public function getInfo(Domain $domain): array
    {
        return [
            'status' => 'active',
            'expires_at' => now()->addYears(5)->toDateString(),
        ];
    }
}
