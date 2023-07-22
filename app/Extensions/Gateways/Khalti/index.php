<?php

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

function Khalti_getConfig()
{
    return [
        [
            'name' => 'secret_key',
            'friendlyName' => 'Khalti Secret Key',
            'type' => 'text',
            'description' => 'Get your secret key from https://khalti.com/merchant/',
            'required' => true,
        ],
        [
            'name' => 'test_mode',
            'friendlyName' => 'Test Mode',
            'type' => 'boolean',
            'description' => 'Enable test mode',
            'required' => false,
        ],
        [
            'name' => 'test_secret_key',
            'friendlyName' => 'Test Secret Key',
            'type' => 'text',
            'description' => 'Get your test secret key from https://khalti.com/merchant/',
            'required' => false,
        ],
    ];
}

function Khalti_pay($total, $products, $invoiceId)
{
    $testmode = ExtensionHelper::getConfig('Khalti', 'test_mode') ? true : false;
    $url = $testmode ? 'https://a.khalti.com/api/v2/epayment/initiate/' : 'https://khalti.com/api/v2/epayment/initiate/';

    $response = Http::withHeaders([
        'Authorization' => 'Key ' . ExtensionHelper::getConfig('Khalti', $testmode ? 'test_secret_key' : 'secret_key'),
        'Content-Type' => 'application/json',
    ])->post($url, [
        'amount' => $total * 100,
        'return_url' => route('khalti.callback', ['invoiceId' => $invoiceId]),
        'website_url' => url('/'),
        'purchase_order_id' => $invoiceId,
        'purchase_order_name' => 'Invoice #' . $invoiceId,

    ]);
    return $response->json()['payment_url'];
}

use Illuminate\Http\Request;

function Khalti_callback($invoiceId, Request $request)
{
    if ($invoiceId !== $request->get('purchase_order_id')) {
        return;
    }
    $testmode = ExtensionHelper::getConfig('Khalti', 'test_mode') ? true : false;
    $url = $testmode ? 'https://a.khalti.com/api/v2/epayment/lookup/' : 'https://khalti.com/api/v2/epayment/lookup/';

    $response = Http::withHeaders([
        'Authorization' => 'Key ' . ExtensionHelper::getConfig('Khalti', $testmode ? 'test_secret_key' : 'secret_key'),
        'Content-Type' => 'application/json',
    ])->post($url, [
        'pidx' => $request->pidx,
    ]);

    if ($response->json()['status'] == 'Completed') {
        ExtensionHelper::paymentDone($invoiceId);
    }
    return redirect()->route('clients.invoice.show', $invoiceId)->with('success', 'Payment has been completed');
}
