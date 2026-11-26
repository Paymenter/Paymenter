<?php

namespace App\Services\BillingAgreement;

use App\Helpers\ExtensionHelper;
use App\Models\BillingAgreement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RemoveBillingAgreementService
{
    /**
     * Remove a billing agreement and detach it from its services.
     *
     * The gateway is asked to revoke the stored token, but a failure there does
     * not block removal: agreements are only ever charged from here, so deleting
     * the row is what stops further charges.
     *
     * @return bool Whether the gateway confirmed the token was revoked
     */
    public function handle(BillingAgreement $billingAgreement): bool
    {
        $revoked = $this->revokeAtGateway($billingAgreement);

        DB::transaction(function () use ($billingAgreement) {
            // Soft deletes don't trigger the foreign key's nullOnDelete
            $billingAgreement->services()->update(['billing_agreement_id' => null]);

            $billingAgreement->delete();
        });

        return $revoked;
    }

    private function revokeAtGateway(BillingAgreement $billingAgreement): bool
    {
        try {
            if ($billingAgreement->gateway && ExtensionHelper::cancelBillingAgreement($billingAgreement)) {
                return true;
            }

            $error = 'The gateway did not confirm the cancellation';
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        Log::warning('Failed to cancel billing agreement at the gateway, removing it anyway', [
            'billing_agreement_id' => $billingAgreement->id,
            'gateway' => $billingAgreement->gateway?->extension,
            'error' => $error,
        ]);

        return false;
    }
}
