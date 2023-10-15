<?php
namespace App\Classes\Extensions;

class Gateway extends Extension {
    /**
     * Get the URL to redirect to
     * 
     * @param int $total
     * @param array $products
     * @param int $invoiceId
     * @return string
     */
    public function pay($total, $products, $invoiceId)
    {
        return '';
    }
}