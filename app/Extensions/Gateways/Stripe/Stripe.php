<?php

namespace App\Extensions\Gateways\Stripe;

use App\Classes\Extension\Gateway;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Stripe extends Gateway
{
    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'stripe_secret_key',
                'label' => 'Stripe Secret Key',
                'type' => 'text',
                'description' => 'Find your API keys at https://dashboard.stripe.com/apikeys',
                'required' => true,
            ],
            [
                'name' => 'stripe_webhook_secret',
                'label' => 'Stripe webhook secret',
                'type' => 'text',
                'description' => 'Stripe webhook secret',
                'required' => true,
            ],
            [
                'name' => 'stripe_publishable_key',
                'label' => 'Stripe Publishable Key',
                'type' => 'text',
                'description' => 'Find your API keys at https://dashboard.stripe.com/apikeys',
                'required' => true,
            ],
            [
                'name' => 'stripe_use_subscriptions',
                'label' => 'Use subscriptions',
                'type' => 'checkbox',
                'description' => 'Enable this option if you want to use subscriptions with Stripe (if available)',
                'required' => false,
            ],
        ];
    }

    private function request($method, $url, $data = [])
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('stripe_secret_key'),
        ])->asForm()->$method('https://api.stripe.com/v1' . $url, $data)->object();
    }

    public function pay($invoice, $total)
    {
        $eligableforSubscription = collect($invoice->items)->filter(function ($item) {
            return $item->service && $item->service->plan->type !== 'one-time';
        })->count() > 0;
        if ($this->config('stripe_use_subscriptions') && $eligableforSubscription) {
            $stripeCustomerId = $invoice->user->properties->where('key', 'stripe_id')->first();
            if (!$stripeCustomerId) {
                $customer = $this->request('post', '/customers', [
                    'email' => $invoice->user->email,
                    'name' => $invoice->user->name,
                    'metadata' => ['user_id' => $invoice->user->id],
                ]);
                $invoice->user->properties()->updateOrCreate(['key' => 'stripe_id'], ['value' => $customer->id]);
            } else {
                $customer = $this->request('get', '/customers/' . $stripeCustomerId->value);
            }

            $intent = $this->request('post', '/setup_intents', [
                'metadata' => ['invoice_id' => $invoice->id],
                'usage' => 'on_session',
                'customer' => $customer->id,
            ]);
            $type = 'setup';
        } else {
            $intent = $this->request('post', '/payment_intents', [
                'amount' => $total * 100,
                'currency' => $invoice->currency_code,
                'automatic_payment_methods' => ['enabled' => 'true'],
                'metadata' => ['invoice_id' => $invoice->id],
            ]);
            $type = 'payment';
        }

        // Pay the invoice using Stripe
        return view('extensions::gateways.stripe.pay', ['invoice' => $invoice, 'total' => $total, 'intent' => $intent, 'type' => $type, 'stripePublishableKey' => $this->config('stripe_publishable_key')]);
    }

    public function webhook(Request $request)
    {
        if (!$this->isValidSignature($request->getContent(), $request->header('Stripe-Signature'), $this->config('stripe_webhook_secret'))) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($request->getContent());

        // Handle the event
        switch ($event->type) {
            // Normal payment
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object; // contains a StripePaymentIntent
                if (!isset($paymentIntent->metadata->invoice_id)) {
                    return response()->json(['error' => 'Invoice ID not found in payment intent metadata'], 400);
                }
                // Get fee from payment intent
                $fee = 0;
                if (isset($paymentIntent->charges->data[0]->balance_transaction)) {
                    $balanceTransaction = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->config('stripe_secret_key'),
                    ])->get('https://api.stripe.com/v1/balance_transactions/' . $paymentIntent->charges->data[0]->balance_transaction)->object();
                    $fee = $balanceTransaction->fee / 100;
                }
                ExtensionHelper::addPayment($paymentIntent->metadata->invoice_id, 'Stripe', $paymentIntent->amount / 100, $fee ?? null, $paymentIntent->id);
                break;
            case 'setup_intent.succeeded':
                $setupIntent = $event->data->object; // contains a StripeSetupIntent
                $this->setupSubscription($setupIntent);
                break;
            case 'subscription_schedule.canceled':
                $subscriptionSchedule = $event->data->object; // contains a StripeSubscriptionSchedule
                $service = Service::where('subscription_id', $subscriptionSchedule->id)->first();
                if ($service) {
                    $service->update(['subscription_id' => null]);
                }
                break;
            case 'invoice.created':
                $invoice = $event->data->object; // contains a StripeInvoice
                // Check if its draft and does exist in our database
                if ($invoice->status === 'draft') {
                    $service = Service::where('subscription_id', $invoice->subscription)->first();

                    if ($service) {
                        $this->request('post', '/invoices/' . $invoice->id . '/finalize');
                        // Pay the invoice using Stripe
                        $this->request('post', '/invoices/' . $invoice->id . '/pay');
                    }
                }
                break;
            case 'invoice.payment_succeeded':
                // Mark invoice as paid
                $invoice = $event->data->object; // contains a StripeInvoice

                $service = Service::where('subscription_id', $invoice->subscription)->first();
                if ($service) {
                    $invoiceModel = $service->invoiceItems->sortByDesc('created_at')->first()->invoice;
                    $paymentIntent = $this->request('get', '/payment_intents/' . $invoice->payment_intent);
                    $fee = 0;
                    if (isset($paymentIntent->charges->data[0]->balance_transaction)) {
                        $balanceTransaction = $this->request('get', '/balance_transactions/' . $paymentIntent->charges->data[0]->balance_transaction);
                        $fee = $balanceTransaction->fee / 100;
                    }
                    ExtensionHelper::addPayment($invoiceModel->id, 'Stripe', $invoice->amount_paid / 100, null, $invoice->payment_intent);
                }
                break;
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }

        http_response_code(200);
    }

    private function setupSubscription($setupIntent)
    {
        $invoice = Invoice::findOrFail($setupIntent->metadata->invoice_id);
        $user = $invoice->user;
        $stripeCustomerId = $user->properties->where('key', 'stripe_id')->first();
        // Create customer if not exists
        if (!$stripeCustomerId) {
            throw new \Exception('Stripe customer not found', $user);
        } else {
            $customer = $this->request('get', '/customers/' . $stripeCustomerId->value);
        }

        // Make payment method default
        $paymentMethod = $this->request('post', '/customers/' . $customer->id, [
            'invoice_settings' => ['default_payment_method' => $setupIntent->payment_method],
        ]);

        // Create subscription
        foreach ($invoice->items as $item) {
            if (!$item->service) {
                continue;
            }
            $service = $item->service;
            $product = $service->product;

            // Check if the service->product already exists in Stripe
            $stripeProduct = $this->request('get', '/products/search', ['query' => 'metadata[\'product_id\']:\'' . $product->id . '\'']);

            if (empty($stripeProduct->data)) {
                // Create product
                $stripeProduct = $this->request('post', '/products', [
                    'name' => $product->name,
                    'metadata' => ['product_id' => $product->id],
                ]);
            } else {
                $stripeProduct = $stripeProduct->data[0];
            }

            $phases = [];
            // Check if current invoice item price is bigger then service price (then we have a setup fee)
            if ($item->price > $service->price) {
                $phases[] = [
                    'items' => [
                        [
                            'price_data' => [
                                'currency' => $invoice->currency_code,
                                'product' => $stripeProduct->id,
                                'unit_amount' => $item->price * 100,
                                'recurring' => [
                                    'interval' => $service->plan->billing_unit,
                                    'interval_count' => $service->plan->billing_period,
                                ],
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'iterations' => 1,
                    'metadata' => [
                        'service_id' => $product->id,
                    ],
                ];
            }
            $phases[] = [
                'items' => [
                    [
                        'price_data' => [
                            'currency' => $invoice->currency_code,
                            'product' => $stripeProduct->id,
                            'unit_amount' => ($service->price * $service->quantity) * 100,
                            'recurring' => [
                                'interval' => $service->plan->billing_unit,
                                'interval_count' => $service->plan->billing_period,
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'service_id' => $product->id,
                ],
            ];

            $subscription = $this->request('post', '/subscription_schedules', [
                'customer' => $customer->id,
                'start_date' => 'now',
                'phases' => $phases,
                'metadata' => ['service_id' => $service->id],
            ]);

            // Update service with subscription id
            $service->update(['subscription_id' => $subscription->subscription]);
            $service->properties()->updateOrCreate(['key' => 'has_stripe_subscription'], ['value' => true]);
        }
    }

    public function updateSubscription(Service $service)
    {
        if (!$service->subscription_id && !$service->properties->where('key', 'has_stripe_subscription')->first()) {
            return;
        }
        // Grab the schedule from Stripe
        $scheduleId = $this->request('get', '/subscriptions/' . $service->subscription_id);
        $oldPhases = $this->request('get', '/subscription_schedules/' . $scheduleId->schedule)->phases;
        // Overwrite phase 2 item 0 with the new price
        $phases = [];
        // Only keep items and end, start date
        foreach ($oldPhases as $phase) {
            $phases[] = [
                'items' => $phase->items,
                'end_date' => $phase->end_date,
                'start_date' => $phase->start_date,
            ];
        }
        // Check if the service->product already exists in Stripe
        $product = $service->product;
        $stripeProduct = $this->request('get', '/products/search', ['query' => 'metadata[\'product_id\']:\'' . $product->id . '\'']);

        if (empty($stripeProduct->data)) {
            // Create product
            $stripeProduct = $this->request('post', '/products', [
                'name' => $product->name,
                'metadata' => ['product_id' => $product->id],
            ]);
        } else {
            $stripeProduct = $stripeProduct->data[0];
        }
        $phases[1]['items'][0]->price_data = [
            'currency' => $service->currency->code,
            'unit_amount' => $service->price * 100,
            'product' => $stripeProduct->id,
            'recurring' => [
                'interval' => $service->plan->billing_unit,
                'interval_count' => $service->plan->billing_period,
            ],
        ];
        $phases[1]['items'][0]->price = null;
        $phases[1]['items'][0]->plan = null;

        // Update the schedule
        $this->request('post', '/subscription_schedules/' . $scheduleId->schedule, [
            'phases' => $phases,
        ]);
    }

    public function cancelSubscription(Service $service)
    {
        if (!$service->subscription_id && !$service->properties->where('key', 'has_stripe_subscription')->first()) {
            return;
        }
        $this->request('delete', '/subscriptions/' . $service->subscription_id);

        return true;
    }

    // Function to split and decode the Stripe-Signature header
    private function getHeaderValues($sig_header)
    {
        $parts = explode(',', $sig_header);
        $timestamp = null;
        $signature = null;

        foreach ($parts as $part) {
            if (strpos($part, 't=') === 0) {
                $timestamp = substr($part, 2);
            } elseif (strpos($part, 'v1=') === 0) {
                $signature = substr($part, 3);
            }
        }

        return [$timestamp, $signature];
    }

    // Validate the signature
    private function isValidSignature($payload, $sig_header, $secret)
    {
        [$timestamp, $signature] = $this->getHeaderValues($sig_header);

        if (empty($timestamp) || empty($signature)) {
            return false;
        }

        // Create the signed payload string
        $signed_payload = $timestamp . '.' . $payload;

        // Compute the expected signature
        $expected_signature = hash_hmac('sha256', $signed_payload, $secret);

        // Compare the expected signature to the actual signature
        return hash_equals($expected_signature, $signature);
    }
}
