<?php

namespace App\Classes;

use App\Models\User;

class Extension
{
    /**
     * Get all the configuration for the extension
     * 
     * @return array
     */
    public static function getConfig()
    {
        return [];
    }

    /**
     * Get the URL to redirect to
     * 
     * @param int $total
     * @param array $products
     * @param int $orderId
     * @return string
     */
    public static function pay($total, $products, $orderId)
    {
        return false;
    }

    /**
     * Create a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public static function createServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        return false;
    }

    /**
     * Suspend a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public static function suspendServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        return false;
    }

    /**
     * Unsuspend a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public static function unsuspendServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        return false;
    }

    /**
     * Terminate a server
     * 
     * @param User $user
     * @param array $params
     * @param Order $order
     * @param OrderProduct $orderProduct
     * @param array $configurableOptions
     * @return bool
     */
    public static function terminateServer($user, $params, $order, $orderProduct, $configurableOptions)
    {
        return false;
    }
}
