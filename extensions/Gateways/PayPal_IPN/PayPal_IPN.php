<?php

namespace Paymenter\Extensions\Gateways\PayPal_IPN;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
