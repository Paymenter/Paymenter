<?php

use App\Helpers\ExtensionHelper;

function PayPal_IPN_pay($total, $products, $orderId)
{
    // Redirect to PayPal
    $paypal_url = ExtensionHelper::getConfig('PayPal_IPN', 'live') ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    $paypal_email = ExtensionHelper::getConfig('PayPal_IPN', 'paypal_email');
    $return_url = route('clients.invoice.show', $orderId);
    $cancel_url = route('clients.invoice.show', $orderId);
    $notify_url = route('paypal_ipn.webhook');
    $currency = ExtensionHelper::getCurrency();
    $item_name = '';
    foreach ($products as $product) {
        $item_name .= $product['name'] . ', ';
    }
    $item_name = substr($item_name, 0, -2);
    $item_number = $orderId;
    // Force amount to be a .00 float
    $amount = number_format($total, 2, '.', '');
    $custom = $orderId;
    $querystring = '?cmd=_xclick&';
    $querystring .= 'business=' . urlencode($paypal_email) . '&';
    $querystring .= 'return=' . urlencode(stripslashes($return_url)) . '&';
    $querystring .= 'cancel_return=' . urlencode(stripslashes($cancel_url)) . '&';
    $querystring .= 'notify_url=' . urlencode($notify_url) . '&';
    $querystring .= 'item_name=' . urlencode($item_name) . '&';
    $querystring .= 'item_number=' . urlencode($item_number) . '&';
    $querystring .= 'amount=' . urlencode($amount) . '&';
    $querystring .= 'custom=' . urlencode($custom) . '&';
    $querystring .= 'currency_code=' . urlencode($currency);

    return $paypal_url . $querystring;
}

function PayPal_IPN_getConfig()
{
    return [
        'paypal_id' => [
            'type' => 'text',
            'name' => 'paypal_email',
            'friendlyName' => 'PayPal Email',
            'description' => 'Your PayPal email address',
            'required' => true,
        ],
        'live' => [
            'type' => 'boolean',
            'name' => 'live',
            'friendlyName' => 'Live',
            'description' => 'Check this box if you want to use the live PayPal API',
            'required' => false,
        ],
    ];
}

function PayPal_IPN_webhook($request)
{
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = [];
    foreach ($raw_post_array as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval) == 2) {
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
    }
    $req = 'cmd=_notify-validate';
    foreach ($myPost as $key => $value) {
        $value = urlencode($value);

        $req .= "&$key=$value";
    }

    if (ExtensionHelper::getConfig('PayPal_IPN', 'live')) {
        $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
    } else {
        $ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
    }
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Connection: Close']);
    if (!($res = curl_exec($ch))) {
        error_log('Got ' . curl_error($ch) . ' when processing IPN data');
        curl_close($ch);
        exit;
    }
    curl_close($ch);
    if ($res == 'VERIFIED') {
        error_log($myPost['custom']);
        ExtensionHelper::paymentDone($myPost['custom']);
    }
    header('HTTP/1.1 200 OK');
}
