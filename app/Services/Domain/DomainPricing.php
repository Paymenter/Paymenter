<?php

namespace App\Services\Domain;

use App\Classes\Price;
use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Domain;
use App\Models\Product;
use App\Models\Tld;
use App\Models\TldPrice;
use Throwable;

class DomainPricing
{
    public static function forCheckout(Tld $tld, string $operation, int $years, ?string $currency = null, ?string $name = null): Price
    {
        if (!$tld->enabled) {
            return self::unavailable();
        }

        return self::price($tld, $operation, $years, $currency, $name);
    }

    public static function forProduct(Product $product, array $config, ?string $currency = null): ?Price
    {
        if (!$product->server || !ExtensionHelper::isRegistrar($product->server)) {
            return null;
        }

        $sld = $config['sld'] ?? null;
        $configuredTld = isset($config['tld']) ? strtolower(ltrim((string) $config['tld'], '.')) : null;
        $name = $sld && $configuredTld
            ? $sld . '.' . $configuredTld
            : ($config['domain'] ?? $config['domain_name'] ?? $config['name'] ?? null);
        $normalizedName = $name ? DomainService::normalizeName($name) : null;
        $tldName = $configuredTld ?: ($normalizedName ? DomainService::tldFromName($normalizedName, $product->server_id) : null);

        if (!$tldName) {
            return self::unavailable();
        }

        if ($normalizedName && $configuredTld && !str_ends_with($normalizedName, '.' . $configuredTld)) {
            return self::unavailable();
        }

        $tld = DomainService::findTldForProduct($tldName, $product);

        if (!$tld) {
            return self::unavailable();
        }

        $mode = $config['mode'] ?? 'register';
        $operation = in_array($mode, ['register', 'transfer'], true) ? $mode : 'register';

        return self::forCheckout($tld, $operation, max(1, (int) ($config['years'] ?? 1)), $currency, $name);
    }

    public static function forRenewal(Domain $domain): Price
    {
        $service = $domain->service;
        $tld = $domain->tldRecord;
        $currency = $service?->currency_code;

        if (!$tld || !$currency) {
            return self::unavailable();
        }

        return self::price($tld, 'renew', $domain->years(), $currency, $domain->name);
    }

    private static function price(Tld $tld, string $operation, int $years, ?string $currency, ?string $name = null): Price
    {
        if (!in_array($operation, ['register', 'renew', 'transfer'], true) || $years < 1) {
            return self::unavailable();
        }

        $currency ??= session('currency', config('settings.default_currency'));

        if ($name) {
            try {
                $registrar = ExtensionHelper::getRegistrar($tld->server);
                $override = $registrar?->price($name, $operation, $years, $currency);
            } catch (Throwable) {
                return self::unavailable();
            }

            if ($override && ($override['currency'] ?? null) === $currency && ($override['price'] ?? null) !== null) {
                $currencyModel = Currency::where('code', $currency)->first();

                if ($currencyModel) {
                    return new Price([
                        'price' => $override['price'],
                        'currency' => $currencyModel,
                    ]);
                }
            }
        }

        $row = TldPrice::where('tld_id', $tld->id)
            ->where('currency_code', $currency)
            ->where('years', $years)
            ->first();

        $amount = $row?->{$operation};

        if (!$row || $amount === null) {
            return self::unavailable();
        }

        return new Price([
            'price' => $amount,
            'currency' => $row->currency,
        ]);
    }

    private static function unavailable(): Price
    {
        return new Price((object) [
            'price' => null,
            'setup_fee' => null,
            'currency' => null,
        ]);
    }
}
