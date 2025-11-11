<?php

namespace App\Policies;

use App\Models\InvoiceTransaction;
use App\Models\User;

class InvoiceTransactionPolicy extends BasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.invoice_transactions.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InvoiceTransaction $invoiceTransaction): bool
    {
        return $this->adminPermission($user, 'admin.invoice_transactions.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.invoice_transactions.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InvoiceTransaction $invoiceTransaction): bool
    {
        return $this->adminPermission($user, 'admin.invoice_transactions.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InvoiceTransaction $invoiceTransaction): bool
    {
        return $user->hasPermission('admin.invoice_transactions.delete');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.invoice_transactions.deleteAny');
    }
}
