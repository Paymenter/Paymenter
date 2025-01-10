<?php

namespace Paymenter\Extensions\Others\Affiliates\Listeners;

use App\Events\User\Created;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;

class ReferUserByAffiliate
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

        $affiliate = Affiliate::where('code', $referral_code)->first();
        if ($affiliate) {
            $affiliate->referrals()->create([
                'user_id' => $event->user->id,
            ]);
        }

        Cookie::forget('referred_by');
    }
}
