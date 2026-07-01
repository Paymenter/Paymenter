<?php

namespace Database\Seeders;

use App\Models\LocationGroup;
use App\Models\LocationOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'us' => LocationGroup::updateOrCreate(
                ['code' => 'us'],
                ['name' => 'US', 'group_type' => LocationGroup::TYPE_COUNTRY_BUNDLE, 'service_types' => ['vps', 'proxy', 'vpn'], 'status' => LocationGroup::STATUS_ACTIVE, 'sort_order' => 10]
            ),
            'eu' => LocationGroup::updateOrCreate(
                ['code' => 'eu'],
                ['name' => 'EU', 'group_type' => LocationGroup::TYPE_REGION, 'service_types' => ['vps', 'proxy', 'vpn'], 'status' => LocationGroup::STATUS_ACTIVE, 'sort_order' => 20]
            ),
            'vn' => LocationGroup::updateOrCreate(
                ['code' => 'vn'],
                ['name' => 'VN', 'group_type' => LocationGroup::TYPE_ISP_BUNDLE, 'service_types' => ['proxy', 'vpn'], 'status' => LocationGroup::STATUS_ACTIVE, 'sort_order' => 30]
            ),
            'other' => LocationGroup::updateOrCreate(
                ['code' => 'other'],
                ['name' => 'Other', 'group_type' => LocationGroup::TYPE_CUSTOM, 'service_types' => ['vps', 'proxy', 'vpn'], 'status' => LocationGroup::STATUS_ACTIVE, 'sort_order' => 100]
            ),
        ];

        foreach ($this->locations($groups) as $location) {
            LocationOption::updateOrCreate(
                ['code' => $location['code']],
                array_merge([
                    'selection_policy' => LocationOption::POLICY_FIXED,
                    'status' => LocationOption::STATUS_ACTIVE,
                    'service_types' => ['vps', 'proxy', 'vpn'],
                    'metadata' => ['source' => 'initial_location_sheet'],
                ], $location)
            );
        }
    }

    private function locations(array $groups): array
    {
        $usStates = [
            [1, 'Nevada', 'NV'],
            [2, 'California', 'CA'],
            [3, 'Texas', 'TX'],
            [4, 'New Jersey', 'NJ'],
            [5, 'Virginia', 'VA'],
            [6, 'New York', 'NY'],
            [7, 'Colorado', 'CO'],
            [8, 'Florida', 'FL'],
            [9, 'Massachusetts', 'MA'],
            [10, 'Illinois', 'IL'],
            [11, 'Washington', 'WA'],
            [14, 'Oregon', 'OR'],
            [15, 'Missouri', 'MO'],
            [19, 'Michigan', 'MI'],
            [20, 'Utah', 'UT'],
            [21, 'North Carolina', 'NC'],
            [29, 'Arizona', 'AZ'],
            [38, 'Wisconsin', 'WI'],
            [39, 'Indiana', 'IN'],
            [45, 'Ohio', 'OH'],
            [49, 'New Mexico', 'NM'],
            [133, 'Pennsylvania', 'PA'],
            [134, 'Delaware', 'DE'],
            [135, 'Arkansas', 'AR'],
        ];

        $locations = [
            $this->location($groups['us'], 0, 'Random US', LocationOption::TYPE_SYNTHETIC_POOL, ['place_country_iso2' => 'US', 'selection_policy' => LocationOption::POLICY_RANDOM]),
            $this->location($groups['other'], 12, 'United Kingdom', LocationOption::TYPE_GEO, ['place_country_iso2' => 'GB']),
            $this->location($groups['other'], 13, 'Unknown', LocationOption::TYPE_UNKNOWN, ['status' => LocationOption::STATUS_HIDDEN]),
            $this->location($groups['eu'], 16, 'Netherlands', LocationOption::TYPE_GEO, ['place_country_iso2' => 'NL']),
            $this->location($groups['other'], 17, 'Georgia', LocationOption::TYPE_GEO, ['metadata' => ['source' => 'initial_location_sheet', 'needs_review' => true]]),
            $this->location($groups['other'], 23, 'Singapore', LocationOption::TYPE_GEO, ['place_country_iso2' => 'SG']),
            $this->location($groups['other'], 24, 'Canada', LocationOption::TYPE_GEO, ['place_country_iso2' => 'CA']),
            $this->location($groups['eu'], 25, 'France', LocationOption::TYPE_GEO, ['place_country_iso2' => 'FR']),
            $this->location($groups['other'], 26, 'Australia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'AU']),
            $this->location($groups['eu'], 27, 'Poland', LocationOption::TYPE_GEO, ['place_country_iso2' => 'PL']),
            $this->location($groups['eu'], 28, 'Germany', LocationOption::TYPE_GEO, ['place_country_iso2' => 'DE']),
            $this->location($groups['eu'], 30, 'Italy', LocationOption::TYPE_GEO, ['place_country_iso2' => 'IT']),
            $this->location($groups['eu'], 31, 'Belgium', LocationOption::TYPE_GEO, ['place_country_iso2' => 'BE']),
            $this->location($groups['eu'], 32, 'Czech Republic', LocationOption::TYPE_GEO, ['place_country_iso2' => 'CZ']),
            $this->location($groups['eu'], 33, 'Finland', LocationOption::TYPE_GEO, ['place_country_iso2' => 'FI']),
            $this->location($groups['eu'], 34, 'Ireland', LocationOption::TYPE_GEO, ['place_country_iso2' => 'IE']),
            $this->location($groups['eu'], 35, 'Lithuania', LocationOption::TYPE_GEO, ['place_country_iso2' => 'LT']),
            $this->location($groups['eu'], 36, 'Portugal', LocationOption::TYPE_GEO, ['place_country_iso2' => 'PT']),
            $this->location($groups['eu'], 37, 'Spain', LocationOption::TYPE_GEO, ['place_country_iso2' => 'ES']),
            $this->location($groups['eu'], 40, 'EU', LocationOption::TYPE_REGION, ['selection_policy' => LocationOption::POLICY_PROVIDER_DECIDES]),
            $this->location($groups['other'], 41, 'Hong Kong', LocationOption::TYPE_GEO, ['place_country_iso2' => 'HK']),
            $this->location($groups['us'], 43, 'US', LocationOption::TYPE_GEO, ['place_country_iso2' => 'US', 'status' => LocationOption::STATUS_DEPRECATED, 'metadata' => ['source' => 'initial_location_sheet', 'alias_of' => 'united-states']]),
            $this->location($groups['other'], 44, 'Canada-Promo', LocationOption::TYPE_PROMO_POOL, ['place_country_iso2' => 'CA']),
            $this->location($groups['other'], 50, 'Mexico', LocationOption::TYPE_GEO, ['place_country_iso2' => 'MX']),
            $this->location($groups['other'], 51, 'Ukraine', LocationOption::TYPE_GEO, ['place_country_iso2' => 'UA']),
            $this->location($groups['other'], 54, 'Argentina', LocationOption::TYPE_GEO, ['place_country_iso2' => 'AR']),
            $this->location($groups['other'], 56, 'Philippines', LocationOption::TYPE_GEO, ['place_country_iso2' => 'PH']),
            $this->location($groups['eu'], 57, 'Latvia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'LV']),
            $this->location($groups['other'], 58, 'Russia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'RU']),
            $this->location($groups['other'], 69, 'Turkey', LocationOption::TYPE_GEO, ['place_country_iso2' => 'TR']),
            $this->location($groups['eu'], 70, 'Romania', LocationOption::TYPE_GEO, ['place_country_iso2' => 'RO']),
            $this->location($groups['other'], 72, 'Japan', LocationOption::TYPE_GEO, ['place_country_iso2' => 'JP']),
            $this->location($groups['eu'], 73, 'Bulgaria', LocationOption::TYPE_GEO, ['place_country_iso2' => 'BG']),
            $this->location($groups['other'], 75, 'Brazil', LocationOption::TYPE_GEO, ['place_country_iso2' => 'BR']),
            $this->location($groups['other'], 76, 'India', LocationOption::TYPE_GEO, ['place_country_iso2' => 'IN']),
            $this->location($groups['us'], 78, 'United States', LocationOption::TYPE_GEO, ['place_country_iso2' => 'US']),
            $this->location($groups['other'], 80, 'Kazakhstan', LocationOption::TYPE_GEO, ['place_country_iso2' => 'KZ']),
            $this->location($groups['other'], 87, 'Belarus', LocationOption::TYPE_GEO, ['place_country_iso2' => 'BY']),
            $this->location($groups['other'], 93, 'China', LocationOption::TYPE_GEO, ['place_country_iso2' => 'CN']),
            $this->location($groups['eu'], 98, 'Sweden', LocationOption::TYPE_GEO, ['place_country_iso2' => 'SE']),
            $this->location($groups['other'], 99, 'South Korea', LocationOption::TYPE_GEO, ['place_country_iso2' => 'KR']),
            $this->location($groups['other'], 102, 'Thailand', LocationOption::TYPE_GEO, ['place_country_iso2' => 'TH']),
            $this->location($groups['other'], 104, 'Malaysia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'MY']),
            $this->location($groups['other'], 106, 'Cambodia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'KH']),
            $this->location($groups['other'], 108, 'Switzerland', LocationOption::TYPE_GEO, ['place_country_iso2' => 'CH']),
            $this->location($groups['other'], 109, 'Norway', LocationOption::TYPE_GEO, ['place_country_iso2' => 'NO']),
            $this->location($groups['other'], 110, 'South Africa', LocationOption::TYPE_GEO, ['place_country_iso2' => 'ZA']),
            $this->location($groups['other'], 111, 'Nigeria', LocationOption::TYPE_GEO, ['place_country_iso2' => 'NG']),
            $this->location($groups['other'], 112, 'Taiwan', LocationOption::TYPE_GEO, ['place_country_iso2' => 'TW']),
            $this->location($groups['other'], 115, 'Armenia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'AM']),
            $this->location($groups['other'], 116, 'Bangladesh', LocationOption::TYPE_GEO, ['place_country_iso2' => 'BD']),
            $this->location($groups['other'], 117, 'Indonesia', LocationOption::TYPE_GEO, ['place_country_iso2' => 'ID']),
            $this->location($groups['eu'], 118, 'Cyprus', LocationOption::TYPE_GEO, ['place_country_iso2' => 'CY']),
            $this->location($groups['eu'], 120, 'Hungary', LocationOption::TYPE_GEO, ['place_country_iso2' => 'HU']),
            $this->location($groups['other'], 121, 'Israel', LocationOption::TYPE_GEO, ['place_country_iso2' => 'IL']),
            $this->location($groups['eu'], 122, 'Denmark', LocationOption::TYPE_GEO, ['place_country_iso2' => 'DK']),
            $this->location($groups['eu'], 124, 'Greece', LocationOption::TYPE_GEO, ['place_country_iso2' => 'GR']),
            $this->location($groups['eu'], 125, 'Malta', LocationOption::TYPE_GEO, ['place_country_iso2' => 'MT']),
            $this->location($groups['other'], 126, 'United Arab Emirates', LocationOption::TYPE_GEO, ['place_country_iso2' => 'AE']),
            $this->location($groups['vn'], 18, 'Viet Nam - Viettel', LocationOption::TYPE_ISP_POOL, ['place_country_iso2' => 'VN', 'isp_name' => 'Viettel', 'service_types' => ['proxy', 'vpn']]),
            $this->location($groups['vn'], 22, 'Viet Nam - Residential', LocationOption::TYPE_NETWORK_POOL, ['place_country_iso2' => 'VN', 'network_type' => LocationOption::NETWORK_RESIDENTIAL, 'service_types' => ['proxy', 'vpn']]),
            $this->location($groups['vn'], 42, 'Viet Nam - Ha Noi', LocationOption::TYPE_GEO, ['place_country_iso2' => 'VN']),
            $this->location($groups['vn'], 46, 'Viet Nam - FPT', LocationOption::TYPE_ISP_POOL, ['place_country_iso2' => 'VN', 'isp_name' => 'FPT', 'service_types' => ['proxy', 'vpn']]),
            $this->location($groups['vn'], 53, 'Viet Nam - CMC', LocationOption::TYPE_ISP_POOL, ['place_country_iso2' => 'VN', 'isp_name' => 'CMC', 'service_types' => ['proxy', 'vpn']]),
            $this->location($groups['vn'], 55, 'Viet Nam - VPN', LocationOption::TYPE_NETWORK_POOL, ['place_country_iso2' => 'VN', 'network_type' => LocationOption::NETWORK_VPN, 'service_types' => ['vpn']]),
            $this->location($groups['vn'], 129, 'Viet Nam - VNPT', LocationOption::TYPE_ISP_POOL, ['place_country_iso2' => 'VN', 'isp_name' => 'VNPT', 'service_types' => ['proxy', 'vpn']]),
            $this->location($groups['vn'], 132, 'Residential - VT', LocationOption::TYPE_ISP_POOL, ['place_country_iso2' => 'VN', 'network_type' => LocationOption::NETWORK_RESIDENTIAL, 'isp_name' => 'Viettel', 'service_types' => ['proxy']]),
        ];

        foreach ($usStates as [$legacyId, $name, $subdivision]) {
            $locations[] = $this->location($groups['us'], $legacyId, $name, LocationOption::TYPE_GEO, [
                'place_country_iso2' => 'US',
                'place_subdivision_code' => $subdivision,
            ]);
        }

        return $locations;
    }

    private function location(LocationGroup $group, int $legacyId, string $name, string $type, array $overrides = []): array
    {
        return array_merge([
            'primary_group_id' => $group->id,
            'legacy_id' => $legacyId,
            'code' => Str::slug($name),
            'display_name' => $name,
            'option_type' => $type,
        ], $overrides);
    }
}
