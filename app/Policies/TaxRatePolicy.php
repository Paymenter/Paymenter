<?php

namespace App\Policies;

use App\Models\TaxRate;
use App\Models\User;

class TaxRatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.tax_rates.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxRate $taxRate): bool
    {
        return $user->hasPermission('admin.tax_rates.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.tax_rates.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxRate $taxRate): bool
    {
        return $user->hasPermission('admin.tax_rates.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxRate $taxRate): bool
    {
        return $user->hasPermission('admin.tax_rates.delete');
    }

    /**
     * Determine whether the user can delete multiple models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.tax_rates.deleteAny');
    }
}
