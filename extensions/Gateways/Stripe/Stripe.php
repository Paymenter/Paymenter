<?php

namespace Paymenter\Extensions\Gateways\Stripe;

use App\Classes\Extension\Gateway;
use App\Events\Service\Updated;
use App\Events\ServiceCancellation\Created;
use App\Helpers\ExtensionHelper;
use App\Models\Gateway as ModelsGateway;
use App\Models\Invoice;
use App\Models\Service;
use Carbon\Carbon;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

class Stripe extends Gateway
{
    public function boot()
    {
        require __DIR__ . '/routes.php';
        // Register webhook route
        View::addNamespace('gateways.stripe', __DIR__ . '/resources/views');

        Event::listen(Updated::class, function (Updated $event) {
            if ($event->service->properties->where('key', 'has_stripe_subscription')->first()?->value !== '1' || !$event->service->subscription_id) {
                // If the service is not a stripe subscription, skip
                return;
            }
            if ($event->service->isDirty('price') || $event->service->isDirty('expires_at')) {
                try {
                    $this->updateSubscription($event->service);
                } catch (Exception $e) {
                }
            }
            // Check if the service is canceled
            if ($event->service->isDirty('status') && $event->service->status === Service::STATUS_CANCELLED) {
                try {
                    $this->cancelSubscription($event->service);
                } catch (Exception $e) {
                    // Ignore exception
                }
            }
        });

        Event::listen(Created::class, function (Created $event) {
            $service = $event->cancellation->service;
            if ($service->properties->where('key', 'has_stripe_subscription')->first()?->value !== '1' || !$service->subscription_id) {
                // If the service is not a stripe subscription, skip
                return;
            }
            try {
                $this->cancelSubscription($service);
            } catch (Exception $e) {
                // Ignore exception
            }
        });
    }

    public function getConfig($values = [])
    {
        return [
            [
                'name' => 'stripe_secret_key',
                'label' => 'Stripe Restricted key',
                'placeholder' => 'Enter your Stripe Restricted API key',
                'type' => 'text',
                'description' => 'Find your API keys at https://dashboard.stripe.com/apikeys',
                'required' => true,
            ],
            [
                'name' => 'stripe_publishable_key',
                'label' => 'Stripe Publishable Key',
                'placeholder' => 'Enter your Stripe Publishable API key',
                'type' => 'text',
                'description' => 'Find your API keys at https://dashboard.stripe.com/apikeys',
                'required' => true,
            ],
            [
                'name' => 'stripe_webhook_secret',
                'label' => 'Stripe webhook secret (auto generated)',
                'type' => 'text',
                'description' => 'Stripe webhook secret',
                'required' => false,
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

    public function updated(ModelsGateway $gateway)
    {
        if (!empty($gateway->settings()->where('key', 'stripe_webhook_secret')->first()->value)) {
            return;
        }

        // Check if webhook already exists
        $webhooks = $this->request('get', '/webhook_endpoints');
        foreach ($webhooks->data as $webhook) {
            if ($webhook->url === route('extensions.gateways.stripe.webhook')) {
                // Delete webhook
                $this->request('delete', '/webhook_endpoints/' . $webhook->id);
                break;
            }
        }

        // Create webhook on stripe
        $webhook = $this->request('post', '/webhook_endpoints', [
            'url' => route('extensions.gateways.stripe.webhook'),
            'description' => 'Paymenter Stripe Webhook',
            'enabled_events' => [
                'payment_intent.succeeded',
                'setup_intent.succeeded',
                'subscription_schedule.canceled',
                'invoice.created',
                'invoice.payment_succeeded',
            ],
            'api_version' => '2025-02-24.acacia', // Use the latest version
        ]);

        $gateway->settings()->updateOrCreate(['key' => 'stripe_webhook_secret'], ['value' => $webhook->secret]);

        Notification::make()
            ->success()
            ->title('Webhook created')
            ->body('We\'ve created a webhook for you on Stripe (refresh the page to see the secret)')
            ->send();
    }

    private function request($method, $url, $data = [])
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config('stripe_secret_key'),
        ])->asForm()->$method('https://api.stripe.com/v1' . $url, $data)->throw()->object();
    }

    public function pay($invoice, $total)
    {
        $eligableforSubscription = collect($invoice->items)->filter(function ($item) {
            return $item->reference_type === Service::class && $item->reference->plan->type !== 'one-time';
        })->count() > 0;
        if ($this->config('stripe_use_subscriptions') && $eligableforSubscription) {
            $stripeCustomerId = $invoice->user->properties->where('key', 'stripe_id')->first();
            if ($stripeCustomerId) {
                try {
                    $customer = $this->request('get', '/customers/' . $stripeCustomerId->value);
                    if ($customer->deleted) {
                        $customer = null;
                    }
                } catch (Exception $e) {
                    // Customer not found, create a new one
                }
            }
            if (!isset($customer)) {
                $customer = $this->request('post', '/customers', [
                    'email' => $invoice->user->email,
                    'name' => $invoice->user->name,
                    'metadata' => ['user_id' => $invoice->user->id],
                ]);
                $invoice->user->properties()->updateOrCreate(['key' => 'stripe_id'], ['value' => $customer->id]);
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
        return view('gateways.stripe::pay', ['invoice' => $invoice, 'total' => $total, 'intent' => $intent, 'type' => $type, 'stripePublishableKey' => $this->config('stripe_publishable_key')]);
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
                    ExtensionHelper::addPayment($invoiceModel->id, 'Stripe', $invoice->amount_paid / 100, $fee ?? null, $invoice->payment_intent);
                }
                break;
            default:
                // Not a event type we care about, just return 200
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
            throw new Exception('Stripe customer not found', $user);
        } else {
            $customer = $this->request('get', '/customers/' . $stripeCustomerId->value);
        }

        // Make payment method default
        $paymentMethod = $this->request('post', '/customers/' . $customer->id, [
            'invoice_settings' => ['default_payment_method' => $setupIntent->payment_method],
        ]);

        // Create subscription
        foreach ($invoice->items as $item) {
            if ($item->reference_type !== Service::class) {
                continue;
            }
            $service = $item->reference;
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
            if ($item->price != $service->price) {
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
                        'service_id' => $service->id,
                    ],
                    'proration_behavior' => 'none',
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
                    'service_id' => $service->id,
                ],
                'proration_behavior' => 'none',
            ];

            $subscription = $this->request('post', '/subscription_schedules', [
                'customer' => $customer->id,
                'start_date' => now()->startOfDay()->timestamp,
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
        // Grab the schedule from Stripe
        $scheduleId = $this->request('get', '/subscriptions/' . $service->subscription_id);

        if ($service->isDirty('price')) {
            if ($scheduleId->schedule) {

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
                // Latest phase is the current one
                $key = count($phases) - 1;
                $phases[$key]['items'][0]->price_data = [
                    'currency' => $service->currency->code,
                    'unit_amount' => $service->price * 100,
                    'product' => $stripeProduct->id,
                    'recurring' => [
                        'interval' => $service->plan->billing_unit,
                        'interval_count' => $service->plan->billing_period,
                    ],
                ];
                $phases[$key]['items'][0]->price = null;
                $phases[$key]['items'][0]->plan = null;

                // Update the schedule
                $this->request('post', '/subscription_schedules/' . $scheduleId->schedule, [
                    'phases' => $phases,
                    'proration_behavior' => 'none',
                ]);
            } else {
                // Get subscription
                $subscription = $this->request('get', '/subscriptions/' . $service->subscription_id);
                // Get first item
                $item = $subscription->items->data[0];
                // Update price
                $this->request('post', '/subscription_items/' . $item->id, [
                    'price_data' => [
                        'currency' => $service->currency->code,
                        'unit_amount' => $service->price * 100,
                        'product' => $item->price->product,
                        'recurring' => [
                            'interval' => $service->plan->billing_unit,
                            'interval_count' => $service->plan->billing_period,
                        ],
                    ],
                    'proration_behavior' => 'none',
                ]);
            }
        }

        if ($service->isDirty('expires_at')) {
            $subDate = Carbon::createFromTimestamp($scheduleId->current_period_end)->startOfDay();
            // Check if current date is before the end date of the subscription
            if ($subDate == $service->expires_at || $service->expires_at <= $subDate) {
                return;
            }

            if ($scheduleId->schedule) {
                // As phases are only used for the setup fee, we can remove the phases
                $this->request('post', '/subscription_schedules/' . $scheduleId->schedule . '/release', []);

                // Get subscription
                $subscription = $this->request('get', '/subscriptions/' . $service->subscription_id);
                // Get first item
                $item = $subscription->items->data[0];
                // Update price
                $this->request('post', '/subscription_items/' . $item->id, [
                    'price_data' => [
                        'currency' => $service->currency->code,
                        'unit_amount' => $service->price * 100,
                        'product' => $item->price->product,
                        'recurring' => [
                            'interval' => $service->plan->billing_unit,
                            'interval_count' => $service->plan->billing_period,
                        ],
                    ],
                    'proration_behavior' => 'none',
                ]);
            }
            // Update the subscription
            $this->request('post', '/subscriptions/' . $service->subscription_id, [
                'trial_end' => $service->expires_at->timestamp,
                'proration_behavior' => 'none',
            ]);
        }
    }

    public function cancelSubscription(Service $service)
    {
        if (!$service->subscription_id && !$service->properties->where('key', 'has_stripe_subscription')->first()) {
            return;
        }
        $this->request('delete', '/subscriptions/' . $service->subscription_id);

        // Remove subscription id from service
        $service->update(['subscription_id' => null]);
        // Remove has_stripe_subscription property
        $service->properties()->where('key', 'has_stripe_subscription')->delete();

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
