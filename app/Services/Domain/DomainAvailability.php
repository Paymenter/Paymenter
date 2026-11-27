<?php

namespace App\Services\Domain;

use App\Helpers\ExtensionHelper;
use App\Models\Product;
use App\Models\Tld;
use Illuminate\Support\Facades\Cache;
use Throwable;

class DomainAvailability
{
    public static function validateCheckout(Product $product, array $config): ?array
    {
        if (!$product->server || !ExtensionHelper::isRegistrar($product->server)) {
            return null;
        }

        if (($config['mode'] ?? 'register') === 'transfer') {
            return null;
        }

        $configuredTld = isset($config['tld']) ? strtolower(ltrim((string) $config['tld'], '.')) : null;
        $name = isset($config['sld']) && $configuredTld
            ? $config['sld'] . '.' . $configuredTld
            : ($config['domain'] ?? $config['domain_name'] ?? $config['name'] ?? null);
        if (!$name) {
            return ['available' => false, 'premium' => false, 'error' => 'A domain name is required.'];
        }

        $name = DomainService::normalizeName($name);

        if ($configuredTld && !str_ends_with($name, '.' . $configuredTld)) {
            return ['available' => false, 'premium' => false, 'error' => 'The domain name does not match the selected TLD.'];
        }

        $tldName = $configuredTld ?: DomainService::tldFromName($name, $product->server_id);
        $tld = DomainService::findTld($tldName, $product->server_id);

        if (!$tld) {
            return ['available' => false, 'premium' => false, 'error' => 'This TLD is not offered.'];
        }

        return self::check($tld, $name, true);
    }

    public static function check(Tld $tld, string $name, bool $fresh = false): array
    {
        $name = DomainService::normalizeName($name);
        $registrar = ExtensionHelper::getRegistrar($tld->server);

        if (!$registrar) {
            return ['available' => false, 'premium' => false, 'error' => 'No registrar is configured for this TLD.'];
        }

        $key = 'domain-availability:' . $tld->server_id . ':' . $name;

        if ($fresh) {
            Cache::forget($key);
        }

        try {
            return Cache::remember($key, now()->addMinutes(10), function () use ($registrar, $name) {
                return $registrar->checkAvailability($name) + ['available' => false, 'premium' => false];
            });
        } catch (Throwable $e) {
            Cache::forget($key);

            return ['available' => false, 'premium' => false, 'error' => $e->getMessage()];
        }
    }
}
