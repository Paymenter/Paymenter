<?php

namespace Paymenter\Extensions\Gateways\PayPal;

use App\Attributes\ExtensionMeta;
use App\Classes\Extension\Gateway;
use App\Events\Service\Updated;
use App\Events\ServiceCancellation\Created;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

#[ExtensionMeta(
    name: 'PayPal Gateway',
    description: 'Accept payments via PayPal.',
    version: '1.0.0',
    author: 'Paymenter',
    url: 'https://paymenter.org/docs/extensions/paypal',
    icon: 'data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNTEyIDUxMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxyZWN0IHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiBmaWxsPSIjRjVGNkY4IiAvPgogICAgPHBhdGggZD0iTTMzNi4zOTcgMTgxLjQ4QzMzNi4zOTcgMjE1LjY2NyAzMDQuODQ3IDI1NiAyNTcuMTExIDI1NkgyMTEuMTI5TDIwOC44NzIgMjcwLjI0MkwxOTguMTQ1IDMzOC44SDE0MUwxNzUuMzc4IDExOEgyNjcuOTYxQzI5OS4xMzcgMTE4IDMyMy42NjQgMTM1LjM3NiAzMzIuNjk4IDE1OS41MjNDMzM1LjMwNCAxNjYuNTQzIDMzNi41NTkgMTczLjk5MyAzMzYuMzk3IDE4MS40OFoiIGZpbGw9IiMwMDI5OTEiIC8+CiAgICA8cGF0aCBkPSJNMzY5LjMzMSAyNDQuOTZDMzYzLjAzMSAyODMuMjM3IDMyOS44OTggMzExLjI5MyAyOTEuMTA2IDMxMS4ySDI1OS4xNzZMMjQ1Ljg4NSAzOTRIMTg5LjA0N0wxOTguMTQzIDMzOC44TDIwOC44NzYgMjcwLjI0MUwyMTEuMTI3IDI1NkgyNTcuMTA5QzMwNC43ODMgMjU2IDMzNi4zOTUgMjE1LjY2NyAzMzYuMzk1IDE4MS40NzlDMzU5Ljg1NSAxOTMuNTg3IDM3My41MzIgMjE4LjA1MyAzNjkuMzMxIDI0NC45NloiIGZpbGw9IiM2MENERkYiIC8+CiAgICA8cGF0aCBkPSJNMzM2LjM5NyAxODEuNDhDMzI2LjU1OSAxNzYuMzM0IDMxNC42MjkgMTczLjIgMzAxLjY0NSAxNzMuMkgyMjQuMTE5TDIxMS4xMjkgMjU2SDI1Ny4xMTFDMzA0Ljc4NSAyNTYgMzM2LjM5NyAyMTUuNjY3IDMzNi4zOTcgMTgxLjQ4WiIgZmlsbD0iIzAwOENGRiIgLz4KPC9zdmc+Cg=='
)]
class PayPal extends Gateway
{
    public function supportsBillingAgreements(): bool
    {
        return $this->config('paypal_support_billing_agreements') ?? false;
    }

    public function createBillingAgreement(\App\Models\User $user)
    {
        // Using PayPal Vaulting
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $result = $this->request('post', $url . '/v3/vault/setup-tokens', [
            'payment_source' => [
                'paypal' => [
                    'description' => 'Billing Agreement for ' . config('settings.company_name'),
                    'usage_type' => 'MERCHANT',
                    'experience_context' => [
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        'return_url' => route('extensions.gateways.paypal.setup-agreement'),
                        'cancel_url' => route('account.payment-methods'),
                        'brand_name' => config('settings.company_name'),
                        'shipping_preference' => 'NO_SHIPPING',
                        'vault_instruction' => 'ON_PAYER_APPROVAL',
                    ],
                ],
            ],
        ]);

        if (!isset($result->links[1]->href)) {
            throw new Exception('Failed to create billing agreement.');
        }

        return $result->links[1]->href;
    }

    public function cancelBillingAgreement(\App\Models\BillingAgreement $billingAgreement): bool
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $this->request('delete', $url . '/v3/vault/payment-tokens/' . $billingAgreement->external_reference);

        return true;
    }

    public function setupAgreement(Request $request)
    {
        $request->validate([
            'approval_token_id' => 'required|string|max:255',
        ]);

        $tokenId = $request->input('approval_token_id');
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $request = $this->request('post', $url . '/v3/vault/payment-tokens', [
            'payment_source' => [
                'token' => [
                    'id' => $tokenId,
                    'type' => 'SETUP_TOKEN',
                ],
            ],
        ]);

        ExtensionHelper::makeBillingAgreement(
            Auth::user(),
            'PayPal',
            'PayPal ' . $request->payment_source->paypal->email_address,
            $request->id,
            'paypal',
        );

        return redirect()->route('account.payment-methods')->with('notification', [
            'type' => 'success',
            'message' => 'Payment method added successfully.',
        ]);
    }

    public function charge(Invoice $invoice, $total, \App\Models\BillingAgreement $billingAgreement)
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
        $result = $this->request('post', $url . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'invoice_id' => $invoice->id,
                    'amount' => [
                        'currency_code' => $invoice->currency_code,
                        'value' => $total,
                    ],
                ],
            ],
            'payment_source' => [
                'paypal' => [
                    'vault_id' => $billingAgreement->external_reference,
                ],
            ],
        ]);

        return true;
    }

    public function boot()
    {
        require __DIR__ . '/routes.php';
        // Register webhook route
        View::addNamespace('gateways.paypal', __DIR__ . '/resources/views');

        Event::listen(Updated::class, function ($event) {
            if ($event->service->properties->where('key', 'has_paypal_subscription')->first()?->value !== '1') {
                return;
            }
            if ($event->service->isDirty('price')) {
                try {
                    $this->updateSubscription($event->service);
                } catch (Exception $e) {
                }
            }
            if ($event->service->isDirty('status') && $event->service->status === Service::STATUS_CANCELLED) {
                try {
                    $this->cancelSubscription($event->service);
                } catch (Exception $e) {
                }
            }
        });

        Event::listen(Created::class, function (Created $event) {
            $service = $event->cancellation->service;
            if ($service->properties->where('key', 'has_paypal_subscription')->first()?->value !== '1') {
                return;
            }
            try {
                $this->cancelSubscription($service);
            } catch (Exception $e) {
                // Log the error or handle it as needed
            }
        });
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
                'name' => 'paypal_support_billing_agreements',
                'label' => 'Supports Billing Agreements',
                'type' => 'checkbox',
                'description' => new HtmlString('Enable this option if your PayPal account supports billing agreements. <a href="https://paymenter.org/docs/extensions/paypal#billing-agreements" target="_blank" rel="noopener">Learn more</a>'),
                'required' => false,
            ],
        ];
    }

    private function generateAccessToken()
    {
        return once(function () {
            $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

            $result = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->config('client_id') . ':' . $this->config('client_secret')),
            ])->asForm()->post($url . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

            if ($result->failed()) {
                throw new Exception('Failed to generate access token: ' . $result->body());
            }

            return $result->json()['access_token'];
        });
    }

    public function request($method, $url, $data = [])
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->generateAccessToken(),
            'PayPal-Request-Id' => uniqid(),
        ])->$method($url, $data)->object();
    }

    public function pay($invoice, $total)
    {
        $url = $this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

        $order = $this->request('post', $url . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'invoice_id' => $invoice->id,
                    'amount' => [
                        'currency_code' => $invoice->currency_code,
                        'value' => $total,
                    ],
                ],
            ],
            'application_context' => [
                'return_url' => route('invoices.show', $invoice),
                'cancel_url' => route('invoices.show', $invoice),
                // Disable shipping information
                'shipping_preference' => 'NO_SHIPPING',
            ],
        ]);

        return view('gateways.paypal::pay', ['invoice' => $invoice, 'total' => $total, 'order' => $order ?? null, 'clientId' => $this->config('client_id')]);
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

        ExtensionHelper::addPayment($order->purchase_units[0]->invoice_id, 'PayPal', $response->purchase_units[0]->payments->captures[0]->amount->value, $response->purchase_units[0]->payments->captures[0]->seller_receivable_breakdown->paypal_fee->value, $response->purchase_units[0]->payments->captures[0]->id);

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
        if ($body['event_type'] === 'BILLING.SUBSCRIPTION.CREATED' && isset($body['resource']['custom_id'])) {
            // Its activated so we can now add the subscription to the user (custom is the order id)
            Invoice::findOrFail($body['resource']['custom_id'])->items()->where('reference_type', Service::class)->each(function (InvoiceItem $item) use ($body) {
                $service = $item->reference;
                $service->subscription_id = $body['resource']['id'];
                $service->save();
                $service->properties()->updateOrCreate([
                    'key' => 'has_paypal_subscription',
                    'name' => 'Has PayPal Subscription',
                ], [
                    'value' => true,
                ]);
            });
        } elseif ($body['event_type'] === 'PAYMENT.SALE.COMPLETED' && isset($body['resource']['billing_agreement_id'])) {
            Service::where('subscription_id', $body['resource']['billing_agreement_id'])->each(function (Service $service) use ($body) {
                // Add payment to the latest invoice
                $latestInvoice = $service->invoices()->latest()->first();
                ExtensionHelper::addPayment($latestInvoice->id, 'PayPal', $body['resource']['amount']['total'], $body['resource']['transaction_fee']['value'], $body['resource']['id']);
            });
        } elseif ($body['event_type'] === 'VAULT.PAYMENT-TOKEN.DELETED') {
            // Find the billing agreement with this external reference
            $billingAgreement = \App\Models\BillingAgreement::where('external_reference', $body['resource']['id'])->first();
            if ($billingAgreement) {
                $billingAgreement->delete();
            }
        } elseif ($body['event_type'] === 'PAYMENT.CAPTURE.COMPLETED' && isset($body['resource']['supplementary_data']['related_ids']['order_id'])) {
            $orderID = $body['resource']['supplementary_data']['related_ids']['order_id'];
            $order = $this->request('get', ($this->config('test_mode') ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com') . '/v2/checkout/orders/' . $orderID);
            if (isset($order->purchase_units[0]->invoice_id)) {
                ExtensionHelper::addPayment($order->purchase_units[0]->invoice_id, 'PayPal', $order->purchase_units[0]->payments->captures[0]->amount->value, $order->purchase_units[0]->payments->captures[0]->seller_receivable_breakdown->paypal_fee->value, $body['resource']['id']);
            }
        }

        return response()->json(['status' => 'success']);
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
                    'currency_code' => $service->currency_code,
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
