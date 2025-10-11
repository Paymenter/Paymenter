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
     * @param \App\Models\Invoice $invoice
     * @param mixed $total
     * @return View|string
     */
    public function pay(Invoice $invoice, $total)
    {
    }

    /**
     * Check if gateway supports billing agreements.
     * 
     * @return bool
     */
    public function supportsBillingAgreements(): bool
    {
        return false;
    }

    /**
     * Create a billing agreement for the given user.
     * 
     * @param \App\Models\User $user
     * @param string $currencyCode
     * @return View|string
     */
    public function createBillingAgreement(User $user)
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Cancel the billing agreement associated with the given card.
     * 
     * @param \App\Models\BillingAgreement $billingAgreement
     * @return void
     */
    public function cancelBillingAgreement(BillingAgreement $billingAgreement): bool
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Charge the given billing agreement for the given invoice and amount.
     * 
     * @param \App\Models\BillingAgreement $billingAgreement
     * @param \App\Models\Invoice $invoice
     * @param mixed $total
     * @return bool
     */
    public function charge(Invoice $invoice, $total, BillingAgreement $billingAgreement)
    {
        throw new \Exception('Not implemented');
    }
}
