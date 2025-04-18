<?php

namespace Paymenter\Extensions\Others\Affiliates\Listeners;

use App\Events\Order\Created;
use Illuminate\Support\Facades\Cookie;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Paymenter\Extensions\Others\Affiliates\Models\AffiliateOrder;

class AssociateOrderWithAffiliate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        $referral_code = Cookie::get('referred_by');

        /** @var Affiliate */
        $affiliate = Affiliate::where('code', $referral_code)->first();
        if (!$affiliate || $affiliate->user->id === $event->order->user_id) {
            return;
        }

        AffiliateOrder::create([
            'order_id' => $event->order->id,
            'affiliate_id' => $affiliate->id,
        ]);
    }
}
