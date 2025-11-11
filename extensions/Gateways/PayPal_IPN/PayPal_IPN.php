<?php

namespace Paymenter\Extensions\Gateways\PayPal_IPN;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

#[ExtensionMeta(
    name: 'PayPal IPN',
    description: 'Accept payments via PayPal. With only your email',
    version: '1.0.0',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/paypal',
    icon: 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxyZWN0IHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiBmaWxsPSIjRjVGNkY4IiAvPgogICAgPHBhdGggZD0iTTMzNi4zOTcgMTgxLjQ4QzMzNi4zOTcgMjE1LjY2NyAzMDQuODQ3IDI1NiAyNTcuMTExIDI1NkgyMTEuMTI5TDIwOC44NzIgMjcwLjI0MkwxOTguMTQ1IDMzOC44SDE0MUwxNzUuMzc4IDExOEgyNjcuOTYxQzI5OS4xMzcgMTE4IDMyMy42NjQgMTM1LjM3NiAzMzIuNjk4IDE1OS41MjNDMzM1LjMwNCAxNjYuNTQzIDMzNi41NTkgMTczLjk5MyAzMzYuMzk3IDE4MS40OFoiIGZpbGw9IiMwMDI5OTEiIC8+CiAgICA8cGF0aCBkPSJNMzY5LjMzMSAyNDQuOTZDMzYzLjAzMSAyODMuMjM3IDMyOS44OTggMzExLjI5MyAyOTEuMTA2IDMxMS4ySDI1OS4xNzZMMjQ1Ljg4NSAzOTRIMTg5LjA0N0wxOTguMTQzIDMzOC44TDIwOC44NzYgMjcwLjI0MUwyMTEuMTI3IDI1NkgyNTcuMTA5QzMwNC43ODMgMjU2IDMzNi4zOTUgMjE1LjY2NyAzMzYuMzk1IDE4MS40NzlDMzU5Ljg1NSAxOTMuNTg3IDM3My41MzIgMjE4LjA1MyAzNjkuMzMxIDI0NC45NloiIGZpbGw9IiM2MENERkYiIC8+CiAgICA8cGF0aCBkPSJNMzM2LjM5NyAxODEuNDhDMzI2LjU1OSAxNzYuMzM0IDMxNC42MjkgMTczLjIgMzAxLjY0NSAxNzMuMkgyMjQuMTE5TDIxMS4xMjkgMjU2SDI1Ny4xMTFDMzA0Ljc4NSAyNTYgMzM2LjM5NyAyMTUuNjY3IDMzNi4zOTcgMTgxLjQ4WiIgZmlsbD0iIzAwOENGRiIgLz4KPC9zdmc+Cg=='
)]
class PayPal_IPN extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
    }

    /**
     * Get all the configuration for the extension
     *
     * @param  array  $values
     * @return array
     */
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'email',
                'label' => 'PayPal Email',
                'type' => 'text',
                'required' => true,
            ],
            [
                'name' => 'test_mode',
                'label' => 'Test Mode',
                'type' => 'checkbox',
                'required' => false,
            ],
        ];
    }

    /**
     * Return a view or a url to redirect to
     *
     * @param  float  $total
     * @return string
     */
    public function pay(Invoice $invoice, $total)
    {
        $paypal_url = $this->config('test_mode') ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        $paypal_email = $this->config('email');
        $return_url = route('invoices.show', $invoice);
        $cancel_url = route('invoices.show', $invoice);

        $notify_url = route('extensions.gateways.paypal_ipn.notify');

        $query = '?cmd=_xclick';
        $query .= '&item_name=' . urlencode('Invoice #' . $invoice->id);
        $query .= '&item_number=' . $invoice->id;
        $query .= '&amount=' . number_format($total, 2, '.', '');
        $query .= '&currency_code=' . $invoice->currency_code;
        $query .= '&business=' . $paypal_email;
        $query .= '&notify_url=' . $notify_url;
        $query .= '&return=' . $return_url;
        $query .= '&cancel_return=' . $cancel_url;

        return $paypal_url . $query;
    }

    /**
     * Handle the IPN request
     *
     * @return void
     */
    public function notify(Request $request)
    {
        // Send the request to PayPal
        $response = Http::asForm()->post($this->config('test_mode') ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr' : 'https://ipnpb.paypal.com/cgi-bin/webscr', [
            'cmd' => '_notify-validate',
        ] + $request->all());

        // Check if the response is verified
        if ($response->body() == 'VERIFIED') {
            ExtensionHelper::addPayment($request->item_number, 'PayPal', $request->mc_gross, $request->mc_fee, transactionId: $request->txn_id);
        }
    }
}
