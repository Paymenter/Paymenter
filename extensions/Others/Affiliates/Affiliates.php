<?php

namespace Paymenter\Extensions\Others\Affiliates;

use App\Classes\Extension\Extension;
use App\Events\User\Created as UserCreated;
use App\Events\Invoice\Paid as InvoicePaid;
use App\Helpers\ExtensionHelper;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Affiliates\Listeners\ReferUserByAffiliate;
use Paymenter\Extensions\Others\Affiliates\Listeners\RewardAffiliate;
use Paymenter\Extensions\Others\Affiliates\Livewire\Affiliates\Affiliate as AffiliateComponent;
use Paymenter\Extensions\Others\Affiliates\Middleware\AffiliatesMiddleware;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

class Affiliates extends Extension
{
    public function __construct(public $config = [])
    {
    }

    /**
     * Get all the configuration for the extension
     *
     * @param array $values
     * @return array
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'default_reward',
                'label' => 'Default Affiliate Reward',
                'type' => 'number',
                'description' => 'Percentage of the purchase amount the affiliated user would receive as a reward.',
                'required' => true,
                'suffix' => '%',
                'validation' => 'integer|min:0|max:100',
            ],
            [
                'name' => 'default_discount',
                'label' => 'Default Affiliate Discount',
                'type' => 'number',
                'suffix' => '%',
                // TODO: Remove `[WIP]`, Make it required, and enable it when this feature is implemented.
                'description' => '[WIP] Discount percentage on products for the affiliated user.',
                'required' => false,
                'disabled' => true,
                'validation' => 'integer|min:0|max:100',
            ],
            [
                'name' => 'type',
                'label' => 'Affiliate Code Type',
                'type' => 'select',
                'default' => 'random',
                'description' => 'How the affiliate would be assigned.',
                'required' => true,
                'options' => [
                    'random' => 'Random',
                    'custom' => 'Custom',
                ],
            ]
        ];
    }

    public function enabled()
    {
        // Run migrations
        Artisan::call('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations/2024_12_25_075634_create_affiliates_table.php']);
        Artisan::call('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations/2024_12_25_092112_create_affiliate_referrals_table.php']);
    }

    public function disabled()
    {
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';
        View::addNamespace('affiliates', __DIR__ . '/resources/views');
        Lang::addNamespace('affiliates', __DIR__ . '/resources/lang');

        Livewire::component('affiliate', AffiliateComponent::class);

        User::resolveRelationUsing('affiliate', function (User $userModel) {
            return $userModel->hasOne(Affiliate::class, 'user_id');
        });

        ExtensionHelper::registerMiddleware(AffiliatesMiddleware::class);

        // Listen for UserCreated and InvoicePaid events
        Event::listen(
            UserCreated::class,
            ReferUserByAffiliate::class,
        );
        Event::listen(
            InvoicePaid::class,
            RewardAffiliate::class,
        );

        // Hook onto account navigation
        Event::listen('navigation.account', function () {
            return [
                'name' => __('affiliates::affiliate.affiliate'),
                'route' => 'affiliate.index',
            ];
        });
    }
}
