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
}
