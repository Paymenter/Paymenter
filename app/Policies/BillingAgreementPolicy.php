<?php

namespace App\Policies;

use App\Models\BillingAgreement;
use App\Models\User;

class BillingAgreementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.billing_agreements.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BillingAgreement $billingAgreement): bool
    {
        return $user->hasPermission('admin.billing_agreements.viewAny');
    }
}
