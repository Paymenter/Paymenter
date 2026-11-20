<?php

namespace App\Services\Domain;

use App\Models\Currency;
use App\Models\Product;
use App\Models\Tld;

class DomainCheckoutService
{
    public static function fields(Product $product): array
    {
        return self::fieldsForProducts([$product]);
    }

    public static function fieldsForProducts(array $products): array
    {
        $tlds = collect($products)
            ->flatMap(fn (Product $product) => self::tldsForProduct($product))
            ->sortBy('tld')
            ->mapWithKeys(fn (Tld $tld) => [$tld->tld => '.' . $tld->tld . ($tld->product?->name ? ' — ' . $tld->product->name : '')])
            ->toArray();

        return [
            [
                'name' => 'sld',
                'label' => 'Domain Name',
                'type' => 'text',
                'description' => 'Enter the name without the TLD, e.g. example',
                'required' => true,
                'validation' => ['string', 'max:63', 'regex:/^[a-z0-9-]+$/i'],
            ],
            [
                'name' => 'tld',
                'label' => 'TLD',
                'type' => 'select',
                'options' => $tlds ?: ['' => 'No TLDs configured'],
                'required' => true,
            ],
            [
                'name' => 'years',
                'label' => 'Registration Period',
                'type' => 'select',
                'options' => [
                    '1' => '1 Year',
                    '2' => '2 Years',
                    '3' => '3 Years',
                    '5' => '5 Years',
                    '10' => '10 Years',
                ],
                'default' => '1',
                'required' => true,
            ],
        ];
    }

    public static function summary(array $config): ?array
    {
        $domain = $config['domain'] ?? null;

        if (!$domain && !empty($config['sld']) && !empty($config['tld'])) {
            $domain = $config['sld'] . '.' . $config['tld'];
        }

        if (!$domain) {
            return null;
        }

        return [
            'domain' => DomainService::normalizeName($domain),
            'years' => max(1, (int) ($config['years'] ?? 1)),
        ];
    }

    public static function checkForProducts(array $products, array $config, bool $fresh = false): array
    {
        $tldName = $config['tld'] ?? null;
        $name = isset($config['sld']) && $tldName
            ? DomainService::normalizeName($config['sld'] . '.' . $tldName)
            : DomainService::normalizeName((string) ($config['domain'] ?? $config['domain_name'] ?? $config['name'] ?? ''));

        $product = collect($products)->first(function (Product $product) use ($tldName, $name) {
            $tld = $tldName ?: DomainService::tldFromName($name);

            return self::tldsForProduct($product)->contains('tld', $tld);
        });

        if (!$product) {
            return ['available' => false, 'premium' => false, 'name' => $name, 'error' => 'This TLD is not offered.'];
        }

        return self::check($product, $config, $fresh) + ['domain_product_id' => $product->id];
    }

    public static function tldsForProduct(Product $product)
    {
        $tlds = Tld::where('enabled', true)
            ->where(function ($query) use ($product) {
                $query->where('product_id', $product->id)
                    ->orWhere(fn ($query) => $query->whereNull('product_id')->where('server_id', $product->server_id));
            })
            ->orderBy('tld')
            ->get();

        return $tlds;
    }

    public static function check(Product $product, array $config, bool $fresh = false): array
    {
        $tldName = $config['tld'] ?? null;
        $sld = $config['sld'] ?? null;

        if ($sld && $tldName) {
            $name = DomainService::normalizeName($sld . '.' . $tldName);
        } else {
            $name = DomainService::normalizeName((string) ($config['domain'] ?? $config['domain_name'] ?? $config['name'] ?? ''));
            $tldName = $tldName ?: DomainService::tldFromName($name, $product->server_id);
        }

        $tld = self::tldsForProduct($product)->firstWhere('tld', $tldName);
        if (!$tld || !$name) {
            return ['available' => false, 'premium' => false, 'name' => $name, 'error' => 'This TLD is not offered.'];
        }

        $years = max(1, (int) ($config['years'] ?? 1));
        $currency = session('currency', config('settings.default_currency')) ?: Currency::query()->value('code');
        $price = DomainPricing::forCheckout($tld, 'register', $years, $currency, $name);

        if (!$price->available) {
            $prices = $tld->prices()->where('currency_code', $currency)->whereNotNull('register');
            $hasCurrency = (clone $prices)->exists();
            $hasYear = (clone $prices)->where('years', $years)->exists();
            $error = $hasCurrency && !$hasYear
                ? 'This registration period is not available.'
                : 'This domain is not available in your currency.';

            return ['available' => false, 'premium' => false, 'name' => $name, 'tld' => $tld->tld, 'years' => $years, 'error' => $error];
        }

        $result = DomainAvailability::check($tld, $name, $fresh);

        return array_merge($result, [
            'name' => $name,
            'tld' => $tld->tld,
            'years' => $years,
            'price' => $price->price,
        ]);
    }
}
