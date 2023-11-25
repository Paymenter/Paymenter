<?php

namespace App\Extensions\Gateways\Stripe;

use App\Classes\Extensions\Gateway;
use Stripe\StripeClient;
use App\Helpers\ExtensionHelper;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

use function Laravel\Prompts\error;

class Stripe extends Gateway
{
    public function getMetadata()
    {
        return [
            'display_name' => 'Stripe',
            'version' => '1.0.0',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    public function getUrl($total, $products, $orderId)
    {
        $subscription = ExtensionHelper::getConfig('Stripe', 'stripe_subscriptions_or_payment') == 'subscriptions' ? true : false;
        $client = $this->stripeClient();
        // Define if all items are subscriptions
        $allSubscriptions = true;
        foreach ($products as $product) {
            if (!$product->billing_cycle && !Str::contains($product->name, 'Setup Fee')) {
                $allSubscriptions = false;
            }
        }
        if ($subscription && $allSubscriptions) {
            $order = $client->checkout->sessions->create([
                // 'line_items' => $items,'
                'currency' => ExtensionHelper::getCurrency(),
                'mode' => 'setup',
                'success_url' => route('clients.invoice.show', $orderId),
                'cancel_url' => route('clients.invoice.show', $orderId),
                'customer_email' => auth()->user()->email,
                'customer_creation' => 'always',
                'metadata' => [
                    'user_id' => auth()->user()->id,
                    'order_id' => $products[0]->order_id ?? null,
                ],
            ]);
        } else {
            // Create array with all the products
            $items = [];
            foreach ($products as $product) {
                $items[] = [
                    'price_data' => [
                        'currency' => ExtensionHelper::getCurrency(),
                        'product_data' => [
                            'name' => $product->name,
                        ],
                        'unit_amount' => round($product->price / $product->quantity * 100, 0),
                    ],
                    'quantity' => $product->quantity,
                ];
            }
            $order = $client->checkout->sessions->create([
                'line_items' => $items,
                'currency' => ExtensionHelper::getCurrency(),
                'mode' => 'payment',
                'success_url' => route('clients.invoice.show', $orderId),
                'cancel_url' => route('clients.invoice.show', $orderId),
                'customer_email' => auth()->user()->email,
                'customer_creation' => 'always',
                'metadata' => [
                    'user_id' => auth()->user()->id,
                    'order_id' => $orderId,
                ],
            ]);
        }

        return $order;
    }

    public function test()
    {

        return view('Stripe::test');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');
        $endpoint_secret = ExtensionHelper::getConfig('Stripe', 'stripe_webhook_secret');
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit;
        }
        if ($event->type == 'checkout.session.completed') {
            if ($event->data->object->mode == 'payment') {
                $order = $event->data->object;
                $order_id = $order->metadata->order_id;
                ExtensionHelper::paymentDone($order_id, 'Stripe', $order->payment_intent);
            }
        }
        // Listen for setup subscription events
        if ($event->type == 'checkout.session.completed') {
            if ($event->data->object->mode !== 'setup') {
                return response()->json(['success' => false]);
            }
            $setupIntent = $event->data->object;
            $user = $setupIntent->customer;
            $products = $setupIntent->metadata->order_id;
            // Trigger the subscription
            $client = $this->stripeClient();
            $paymentMethod = $client->setupIntents->retrieve(
                $setupIntent->setup_intent,
                []
            );

            $customer = $client->customers->allPaymentMethods(
                $user,
                []
            );

            if (!isset($customer->data[0]->id)) {
                Log::error('No payment method found for customer: ' . $user);
                return response()->json(['success' => false]);
            }

            // Set $paymentMethod as the default for future invoices
            $client->customers->update(
                $user,
                [
                    'invoice_settings' => [
                        'default_payment_method' => $customer->data[0]->id,
                    ],
                ]
            );

            foreach (Order::find($products)->products as $product) {
                $sproduct = $client->products->search([
                    'query' => 'metadata[\'product_id\']:\'' . $product->product->id . '\'',
                ]);
                if (count($sproduct->data) == 0) {
                    $sproduct = $client->products->create([
                        'name' => $product->product->name,
                        'metadata' => [
                            'product_id' => $product->product->id,
                        ],
                    ]);
                } else {
                    $sproduct = $sproduct->data[0];
                }
                $recurring  = [];
                $billing_cycle = $product->billing_cycle;
                if ($billing_cycle == 'monthly') {
                    $recurring = [
                        'interval' => 'month',
                    ];
                } else if ($billing_cycle == 'quarterly') {
                    $recurring = [
                        'interval' => 'month',
                        'interval_count' => 3,
                    ];
                } else if ($billing_cycle == 'semi_annually') {
                    $recurring = [
                        'interval' => 'month',
                        'interval_count' => 6,
                    ];
                } elseif ($billing_cycle == 'biennially') {
                    $recurring = [
                        'interval' => 'year',
                        'interval_count' => 2,
                    ];
                } elseif ($billing_cycle == 'triennially') {
                    $recurring = [
                        'interval' => 'year',
                        'interval_count' => 3,
                    ];
                } elseif ($billing_cycle == 'yearly') {
                    $recurring = [
                        'interval' => 'year',
                    ];
                }
                $phases = [];
                if ($product->product->price($billing_cycle . '_setup') > 0) {
                    $phases[] = [
                        'items' => [
                            [
                                'price_data' => [
                                    'currency' => ExtensionHelper::getCurrency(),
                                    'product' => $sproduct->id,
                                    'unit_amount' => round(($product->product->price($product->billing_cycle) + $product->product->price($product->billing_cycle . '_setup')) / $product->quantity * 100, 0),
                                    'recurring' => $recurring,
                                ],
                                'quantity' => $product->quantity,
                            ],
                        ],
                        'metadata' => [
                            'order_product_id' => $product->id,
                        ],
                        'iterations' => 1,
                    ];
                }
                $phases[] = [
                    'items' => [
                        [
                            'price_data' => [
                                'currency' => ExtensionHelper::getCurrency(),
                                'product' => $sproduct->id,
                                'unit_amount' => round($product->product->price($product->billing_cycle) / $product->quantity * 100, 0),
                                'recurring' => $recurring,
                            ],
                            'quantity' => $product->quantity,
                        ],
                    ],
                    'metadata' => [
                        'order_product_id' => $product->id,
                    ],
                    'iterations' => $product->product->price($billing_cycle . '_setup') > 0 ? 1 : 0,
                ];
                $client->subscriptionSchedules->create([
                    'customer' => $paymentMethod->customer,
                    'start_date' => 'now',
                    'phases' => $phases,
                    'metadata' => [
                        'order_product_id' => $product->id,
                    ],
                ]);
            }
        }
        if ($event->type == 'customer.subscription.created') {
            $subscription = $event->data->object;
            $order_product_id = $subscription->metadata->order_product_id;
            $order_product = OrderProduct::find($order_product_id);
            ExtensionHelper::setOrderProductConfig('stripe_subscription_id', $subscription->id, $order_product_id);
            // If the subscription is active mark invoice as paid
            if ($subscription->status == 'active') {
                $invoice = $order_product->lastInvoice();
                ExtensionHelper::paymentDone($invoice->id, 'Stripe', $subscription->id);
            }
        }
        // Listen for subscription events (failed, canceled, etc.)
        if ($event->type == 'invoice.payment_failed') {
            $invoice = $event->data->object;
            $client = $this->stripeClient();
            $subscription = $client->subscriptions->retrieve(
                $invoice->subscription,
                []
            );
            $order_product_id = $subscription->metadata->order_product_id;
            // We need to figure this out still
            //ExtensionHelper::paymentFailed($invoice->id, 'Stripe', $invoice->subscription);

        }
        if ($event->type == 'invoice.payment_succeeded') {
            $invoice = $event->data->object;
            $subscription = $invoice->subscription;
            $client = $this->stripeClient();
            $subscription = $client->subscriptions->retrieve(
                $subscription,
                []
            );
            $order_product_id = $subscription->metadata->order_product_id;
            $invoice = OrderProduct::find($order_product_id)->lastInvoice();
            ExtensionHelper::paymentDone($invoice->id, 'Stripe', $invoice->id);
        }

        return response()->json(['success' => true]);
    }

    public function stripeClient()
    {
        if (!ExtensionHelper::getConfig('Stripe', 'stripe_test_mode')) {
            $stripe = new StripeClient(
                ExtensionHelper::getConfig('Stripe', 'stripe_secret_key')
            );
        } else {
            $stripe = new StripeClient(
                ExtensionHelper::getConfig('Stripe', 'stripe_test_key')
            );
        }

        return $stripe;
    }

    public function pay($total, $products, $orderId)
    {
        $stripe = $this->stripeClient();
        $order = $this->getUrl($total, $products, $orderId);

        return $stripe->checkout->sessions->retrieve($order->id, [])->url;
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'stripe_secret_key',
                'friendlyName' => 'Stripe Secret Key',
                'type' => 'text',
                'description' => 'Stripe secret key',
                'required' => true,
            ],
            [
                'name' => 'stripe_webhook_secret',
                'friendlyName' => 'Stripe webhook secret',
                'type' => 'text',
                'description' => 'Stripe webhook secret',
                'required' => true,
            ],
            [
                'name' => 'stripe_test_mode',
                'friendlyName' => 'Stripe test mode',
                'type' => 'boolean',
                'description' => 'Stripe test mode',
                'required' => false,
            ],
            [
                'name' => 'stripe_test_key',
                'friendlyName' => 'Stripe test key',
                'type' => 'text',
                'description' => 'Stripe test key',
                'required' => false,
            ],
            [
                'name' => 'stripe_subscriptions_or_payment',
                'friendlyName' => 'Stripe subscriptions or payment',
                'type' => 'dropdown',
                'options' => [
                    [
                        'name' => 'Subscriptions',
                        'value' => 'subscriptions',
                    ],
                    [
                        'name' => 'Payment',
                        'value' => 'payment',
                    ]
                ],
                'description' => 'Stripe subscriptions or payment',
            ]
        ];
    }
}
