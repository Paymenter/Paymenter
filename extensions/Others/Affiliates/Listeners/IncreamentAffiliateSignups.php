<?php

namespace Paymenter\Extensions\Others\Affiliates\Listeners;

use App\Events\User\Created;
use Illuminate\Support\Facades\Cookie;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

class IncreamentAffiliateSignups
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
        if (!$affiliate) {
            return;
        }

        $affiliate->increment('signups');
    }
}
