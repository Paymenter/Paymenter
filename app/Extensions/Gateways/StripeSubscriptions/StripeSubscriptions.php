<?php
namespace App\Extensions\Gateways\StripeSubscriptions;

use App\Classes\Extensions\Gateway;
use Illuminate\Http\Request;
use App\Extensions\Gateways\Stripe\Stripe;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use App\Models\Order;
use App\Models\OrderProduct;
use Stripe\StripeClient;
use Illuminate\Support\Str;

class StripeSubscriptions extends Gateway {

    public function getMetadata()
    {
        return [
            'display_name' => 'Stripe Subscriptions',
            'version' => '2.0.1',
            'author' => 'Paymenter',
            'website' => 'https://paymenter.org',
        ];
    }

    public function getConfig(){
        return [];
    }

    public function canUse($total, $products)
    {
        $allSubscriptions = true;
        foreach ($products as $product) {
            if (!$product->billing_cycle && !Str::contains($product->name, 'Setup Fee')) {
                $allSubscriptions = false;
            }
        }
        return $allSubscriptions;
    }

    public function pay($_, $products, $orderId)
    {
        $client = $this->stripeClient();
        // Define if all items are subscriptions
        $allSubscriptions = true;
        foreach ($products as $product) {
            if (!$product->billing_cycle && !Str::contains($product->name, 'Setup Fee')) {
                $allSubscriptions = false;
            }
        }
        if ($allSubscriptions) {
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
            $stripe = new Stripe(Extension::where('name', 'Stripe')->first());
            return $stripe->pay($_, $products, $orderId);
        }

        return $client->checkout->sessions->retrieve($order->id, [])->url;
    }

    public function stripeClient()
    {
        if (!ExtensionHelper::getConfig('Stripe', 'stripe_test_mode')) {
            return new StripeClient(
                ExtensionHelper::getConfig('Stripe', 'stripe_secret_key')
            );
        } else {
            return new StripeClient(
                ExtensionHelper::getConfig('Stripe', 'stripe_test_key')
            );
        }
    }

    public function webhook($event){
        $client = $this->stripeClient();
        // Listen for setup subscription events
        if ($event->type == 'checkout.session.completed') {
            if ($event->data->object->mode !== 'setup') {
                return response()->json(['success' => false]);
            }
            $setupIntent = $event->data->object;
            $user = $setupIntent->customer;
            $products = $setupIntent->metadata->order_id;
            // Trigger the subscription
            $paymentMethod = $client->setupIntents->retrieve(
                $setupIntent->setup_intent,
                []
            );

            $customer = $client->customers->allPaymentMethods(
                $user,
                []
            );

            if (!isset($customer->data[0]->id)) {
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
    }
}