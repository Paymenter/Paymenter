<?php

namespace Paymenter\Extensions\Others\Affiliates\Listeners;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Support\Collection;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Paymenter\Extensions\Others\Affiliates\Models\AffiliateOrder;

class RewardAffiliate
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
    public function handle(object $event): void
    {
        /**
         * @var Invoice $invoice
         */
        $invoice = $event->invoice;

        if ($invoice->items()->first()->reference_type !== Service::class) {
            return;
        }
        $order = $invoice->items()->first()->reference->order;
        if (!$order) {
            return;
        }
        $referral = AffiliateOrder::where('order_id', $order->id)->first();

        if (!$referral) {
            return;
        }

        /**
         * @var Affiliate $affiliate
         */
        $affiliate = $referral->affiliate;
        $extension = ExtensionHelper::getExtension('other', 'Affiliates');
        $reward_percentage = $affiliate->reward ?: $extension->config('default_reward');
        $reward_amount = $invoice->total * $reward_percentage / 100;

        /**
         * @var Collection
         */
        $user_credits = $affiliate->user->credits;
        $affiliate_credits = $user_credits->filter(function ($credit) use ($invoice) {
            return $credit->currency_code === $invoice->currency_code;
        })->first();

        if ($affiliate_credits) {
            // Add reward to credits
            $affiliate->user->credits()->where('currency_code', $invoice->currency_code)->update([
                'amount' => $affiliate_credits->amount + $reward_amount,
            ]);
        } else {
            // Create new credits with the invoice's currency code
            $affiliate->user->credits()->create([
                'amount' => $reward_amount,
                'currency_code' => $invoice->currency_code,
            ]);
        }
    }
}
