<?php

namespace App\Classes\Extension;

use App\Models\BillingAgreement;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\View;

/**
 * Class Gateway
 */
abstract class Gateway extends Extension
{
    /**
     * Pay the given invoice with the given total amount.
     *
     * @param  mixed  $total
     * @return View|string
     */
    abstract public function pay(Invoice $invoice, $total);

    /**
     * Check if gateway supports billing agreements.
     */
    public function supportsBillingAgreements(): bool
    {
        return false;
    }

    /**
     * Create a billing agreement for the given user.
     *
     * @param  string  $currencyCode
     * @return View|string
     */
    public function createBillingAgreement(User $user)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Cancel the billing agreement associated with the given card.
     *
     * @return void
     */
    public function cancelBillingAgreement(BillingAgreement $billingAgreement): bool
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Charge the given billing agreement for the given invoice and amount.
     *
     * @param  mixed  $total
     * @return bool
     */
    public function charge(Invoice $invoice, $total, BillingAgreement $billingAgreement)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Verify a payment for the given invoice directly with the gateway.
     *
     * Called when the customer returns from an off-site / redirect checkout, so
     * that activation does not depend on an asynchronous webhook (which may be
     * unreachable in local / test environments). Implementations decide for
     * themselves — based on the current request — whether they apply, and must
     * be idempotent: recording the same payment again is safe. Default: no-op.
     */
    public function checkPayment(Invoice $invoice): void
    {
        // No-op by default; gateways that redirect off-site should override this.
    }
}
