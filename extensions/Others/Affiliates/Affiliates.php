<?php

namespace Paymenter\Extensions\Others\Affiliates;

use App\Classes\Extension\Extension;
use App\Events\Invoice\Paid as InvoicePaid;
use App\Events\Order\Created as OrderCreated;
use App\Events\User\Created as UserCreated;
use App\Helpers\ExtensionHelper;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Affiliates\Listeners\AssociateOrderWithAffiliate;
use Paymenter\Extensions\Others\Affiliates\Listeners\IncreamentAffiliateSignups;
use Paymenter\Extensions\Others\Affiliates\Listeners\RewardAffiliate;
use Paymenter\Extensions\Others\Affiliates\Livewire\Affiliates\Affiliate as AffiliateComponent;
use Paymenter\Extensions\Others\Affiliates\Middleware\AffiliatesMiddleware;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

class Affiliates extends Extension
{
    public function __construct(public $config = []) {}

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
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
                'name' => 'cookie_max_age',
                'label' => 'Referral Cookie Max-Age',
                'type' => 'number',
                'description' => 'Amount of days for which the referral cookie be valid. (Set 0 for infinite)',
                'required' => true,
                'validation' => 'integer|min:0',
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
            ],
        ];
    }

    public function enabled()
    {
        // Run migrations
        Artisan::call('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations/2024_12_25_075634_create_ext_affiliates_table.php', '--force' => true]);
        Artisan::call('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations/2025_01_31_155928_create_ext_affiliate_orders_table.php', '--force' => true]);
    }

    public function disabled() {}

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
            IncreamentAffiliateSignups::class,
        );
        Event::listen(
            InvoicePaid::class,
            RewardAffiliate::class,
        );
        Event::listen(
            OrderCreated::class,
            AssociateOrderWithAffiliate::class,
        );

        Event::listen('api.permissions', function () {
            return [
                'admin.affiliates.view' => 'View Affiliates',
                'admin.affiliates.create' => 'Create Affiliates',
                'admin.affiliates.update' => 'Update Affiliates',
                'admin.affiliates.delete' => 'Delete Affiliates',
            ];
        });

        // Event::listen('navigation.dashboard', function ($routes) {
        //     dd($routes);
        //     return [
        //         'name' => __('affiliates::affiliate.affiliate'),
        //         'route' => 'affiliate.index',
        //         'icon' => 'heroicon-o-banknotes',
        //         'group' => 'Administration',
        //     ];
        // });

        // Hook onto account navigation
        Event::listen('navigation.account', function () {
            return [
                'name' => __('affiliates::affiliate.affiliate'),
                'route' => 'affiliate.index',
            ];
        });
    }
}
