<?php

namespace App\Policies;

use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('admin.order_product.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderProduct $orderProduct): bool
    {
        return $user->hasPermission('admin.order_product.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('admin.order_product.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderProduct $orderProduct): bool
    {
        return $user->hasPermission('admin.order_product.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderProduct $orderProduct): bool
    {
        return $user->hasPermission('admin.order_product.delete');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermission('admin.order_product.deleteAny');
    }
}
