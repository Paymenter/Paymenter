<?php

namespace Paymenter\Extensions\Gateways\PayPal;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class PayPal extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        // Register webhook route
        View::addNamespace('gateways.paypal', __DIR__ . '/resources/views');
    }

    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'client_id',
                'label' => 'Client ID',
                'type' => 'text',
                'description' => 'Find your API keys at https://developer.paypal.com/developer/applications',
                'required' => true,
            ],
            [
                'name' => 'client_secret',
                'label' => 'Client Secret',
                'type' => 'text',
                'description' => 'Find your API keys at https://developer.paypal.com/developer/applications',
                'required' => true,
            ],
            [
                'name' => 'webhook_id',
                'label' => 'Webhook ID',
                'type' => 'text',
                'description' => 'Find your webhook ID at https://developer.paypal.com/developer/webhooks',
                'required' => true,
            ],
            [
                'name' => 'test_mode',
                'label' => 'Test Mode',
                'type' => 'checkbox',
                'description' => 'Enable test mode',
                'required' => false,
            ],
            [
                'name' => 'paypal_use_subscriptions',
                'label' => 'Use subscriptions',
                'type' => 'checkbox',
                'description' => 'Enable this option if you want to use subscriptions with PayPal (if available)',
                'required' => false,
            ],
        ];
    }

    private function generateAccessToken()
    {
        return once(function () {
            $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

            return Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->config('client_id') . ':' . $this->config('client_secret')),
            ])->asForm()->post($url . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ])->object()->access_token;
        });
    }

    public function request($method, $url, $data = [])
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->generateAccessToken(),
        ])->$method($url, $data)->object();
    }

    public function pay($invoice, $total)
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        if ($this->config('paypal_use_subscriptions') && $invoice->items->map(fn ($item) => $item->service->plan->billing_period . $item->service->plan->billing_unit)->unique()->count() === 1) {
            $paypalProduct = $this->request('post', $url . '/v1/catalogs/products', [
                'name' => $invoice->items->first()->service->product->name,
                'type' => 'SERVICE',
            ]);

            // For each item in the invoice, create a billing cycle
            $billingCycles[] = [
                'frequency' => [
                    'interval_unit' => strtoupper($invoice->items->first()->service->plan->billing_unit),
                    'interval_count' => $invoice->items->first()->service->plan->billing_period,
                ],
                'tenure_type' => 'TRIAL',
                'sequence' => 1,
                'total_cycles' => 1,
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => $total,
                        'currency_code' => $invoice->currency_code,
                    ],
                ],
            ];

            $nextSum = $invoice->items->sum(fn ($item) => $item->service->price * $item->service->quantity);

            $billingCycles[] = [
                'frequency' => [
                    'interval_unit' => strtoupper($invoice->items->first()->service->plan->billing_unit),
                    'interval_count' => $invoice->items->first()->service->plan->billing_period,
                ],
                'tenure_type' => 'REGULAR',
                'sequence' => 2,
                'total_cycles' => 0,
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => $nextSum,
                        'currency_code' => $invoice->currency_code,
                    ],
                ],
            ];

            $plan = $this->request('post', $url . '/v1/billing/plans', [
                'product_id' => $paypalProduct->id,
                'name' => $invoice->items->first()->service->plan->name,
                'description' => $invoice->items->first()->service->plan->description,
                'billing_cycles' => $billingCycles,
                'payment_preferences' => [
                    'auto_bill_outstanding' => true,
                    'setup_fee_failure_action' => 'CONTINUE',
                    'payment_failure_threshold' => 3,
                ],
            ]);
        } else {
            $order = $this->request('post', $url . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $invoice->id,
                        'amount' => [
                            'currency_code' => $invoice->currency_code,
                            'value' => $total,
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('invoices.show', ['invoice' => $invoice->id]),
                    'cancel_url' => route('invoices.show', ['invoice' => $invoice->id]),
                    // Disable shipping information
                    'shipping_preference' => 'NO_SHIPPING',
                ],
            ]);
        }

        return view('gateways.paypal::pay', ['invoice' => $invoice, 'total' => $total, 'order' => $order ?? null, 'plan' => $plan ?? null, 'clientId' => $this->config('client_id')]);
    }

    public function capture(Request $request)
    {
        if (!$request->has('orderID')) {
            abort(400);
        }
        $orderID = $request->input('orderID');
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        // First check if the order is already captured
        $order = $this->request('get', $url . '/v2/checkout/orders/' . $orderID);
        if ($order->status === 'COMPLETED') {
            return $order;
        }

        $response = $this->request('post', $url . '/v2/checkout/orders/' . $orderID . '/capture', [
            'intent' => 'CAPTURE',
        ]);

        ExtensionHelper::addPayment($response->purchase_units[0]->reference_id, 'PayPal', $response->purchase_units[0]->payments->captures[0]->amount->value, $response->purchase_units[0]->payments->captures[0]->seller_receivable_breakdown->paypal_fee->value, $response->id);

        return $response;
    }

    public function webhook(Request $request)
    {
        $body = $request->getContent();
        $sigString = $request->header('PAYPAL-TRANSMISSION-ID') . '|' . $request->header('PAYPAL-TRANSMISSION-TIME') . '|' . $this->config('webhook_id') . '|' . crc32($body);
        $pubKey = openssl_pkey_get_public(file_get_contents($request->header('PAYPAL-CERT-URL')));
        $details = openssl_pkey_get_details($pubKey);
        $verifyResult = openssl_verify(
            $sigString,
            base64_decode(
                $request->header('PAYPAL-TRANSMISSION-SIG')
            ),
            $details['key'],
            'sha256WithRSAEncryption'
        );
        if ($verifyResult !== 1) {
            return response()->json(['status' => 'error']);
        }

        $body = $request->json()->all();

        // Handle the subscription event
        if ($body['event_type'] === 'BILLING.SUBSCRIPTION.ACTIVATED') {
            // Its activated so we can now add the subscription to the user (custom is the order id)
            Order::find($body['resource']['custom_id'])->services->each(function ($service) use ($body) {
                $service->subscription_id = $body['resource']['id'];
                $service->save();
                $service->properties()->updateOrCreate([
                    'key' => 'has_paypal_subscription',
                    'name' => 'Has PayPal Subscription',
                ], [
                    'value' => true,
                ]);
            });

            return response()->json(['status' => 'success']);
        } elseif ($body['event_type'] === 'PAYMENT.SALE.COMPLETED') {
            $order = Order::findOrFail($body['resource']['custom']);
            foreach ($order->services as $service) {
                // Get last invoice item
                $invoiceItem = $service->invoiceItems->last();
                // Add payment
                ExtensionHelper::addPayment($invoiceItem->invoice_id, 'PayPal', $body['resource']['amount']['total'], $body['resource']['transaction_fee']['value'], $body['resource']['id']);
            }
        }
    }

    public function updateSubscription(Service $service)
    {
        if ($service->properties->where('key', 'has_paypal_subscription')->first()?->value !== '1') {
            return false;
        }
        $paypal = new PayPal;
        // Update subscription price
        $newPrice = Service::where('subscription_id', $service->subscription_id)->sum('price');
        // Grab currenct subscription ID
        $subscriptionId = $service->subscription_id;
        $url = $paypal->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        // Update subscription price
        $paypal->request('PATCH', $url . '/v1/billing/subscriptions/' . $subscriptionId, [
            [
                'op' => 'replace',
                'path' => '/plan/billing_cycles/@sequence==2/pricing_scheme/fixed_price',
                'value' => [
                    'value' => $newPrice,
                    'currency_code' => $service->order->currency_code,
                ],
            ],
        ]);

        return true;
    }

    public function cancelSubscription(Service $service)
    {
        // Cancel subscription
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $this->request('post', $url . '/v1/billing/subscriptions/' . $service->subscription_id . '/cancel', [
            'reason' => 'User canceled',
        ]);

        $service->properties()->where('key', 'has_paypal_subscription')->delete();

        return true;
    }
}
