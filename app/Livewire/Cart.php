<?php

namespace App\Livewire;

use App\Classes\Cart as ClassesCart;
use App\Classes\Price;
use App\Exceptions\DisplayException;
use App\Helpers\ExtensionHelper;
use App\Jobs\Server\CreateJob;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;

class Cart extends Component
{
    #[Locked]
    public $total;

    public $gateway;

    public $coupon;

    public $use_credits = true;

    public $tos;

    public function mount()
    {
        if (ClassesCart::get()->coupon_id) {
            $this->coupon = ClassesCart::get()->coupon;
        }
        $this->updateTotal();
    }

    private function updateTotal()
    {
        if (ClassesCart::items()->count() == 0) {
            $this->total = null;

            return;
        }
        $this->total = new Price(['price' => ClassesCart::items()->sum(fn ($item) => $item->price->total * $item->quantity), 'currency' => ClassesCart::get()->currency]);
        $this->gateways = ExtensionHelper::getCheckoutGateways($this->total->total, $this->total->currency->code, 'cart', ClassesCart::items());
        if (count($this->gateways) > 0 && !array_search($this->gateway, array_column($this->gateways, 'id')) !== false) {
            $this->gateway = $this->gateways[0]->id;
        }
    }

    public function applyCoupon()
    {
        if ($this->coupon && ClassesCart::get()->coupon_id) {
            return $this->notify('Coupon code already applied', 'error');
        }
        try {
            $cart = ClassesCart::applyCoupon($this->coupon);
        } catch (DisplayException $e) {
            $this->notify($e->getMessage(), 'error');
            $this->coupon = null;

            return;
        }
        $this->coupon = $cart->coupon;
        $this->updateTotal();
        $this->notify('Coupon code applied successfully', 'success');
    }

    public function removeCoupon()
    {
        if (!$this->coupon || !ClassesCart::get()->coupon_id) {
            return $this->notify('No coupon code applied', 'error');
        }
        ClassesCart::removeCoupon();
        $this->coupon = null;
        $this->updateTotal();
        $this->notify('Coupon code removed successfully', 'success');
    }

    public function removeProduct($index)
    {
        ClassesCart::remove($index);
        $this->updateTotal();
    }

    public function updateQuantity($index, $quantity)
    {
        ClassesCart::updateQuantity($index, $quantity);
        $this->updateTotal();
    }

    // Checkout
    public function checkout()
    {
        if (ClassesCart::items()->count() === 0) {
            return $this->notify('Your cart is empty', 'error');
        }
        if (!Auth::check()) {
            return redirect()->guest('login');
        }
        if (config('settings.mail_must_verify') && !Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        if (config('settings.tos') && !$this->tos) {
            return $this->notify('You must accept the terms of service', 'error');
        }

        // Re-validate coupon if one exists
        if (ClassesCart::get()->coupon && !ClassesCart::validateAndRefreshCoupon()) {
            $this->coupon = null;
            $this->updateTotal();

            return $this->notify('This coupon can no longer be used', 'error');
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            $cart = ClassesCart::get();

            $user = User::where('id', Auth::id())->lockForUpdate()->first();
            // Lock the orderproducts
            foreach ($cart->items as $item) {
                // Make sure we have the latest product data and lock it
                $item->product->lockForUpdate();

                if (
                    $item->product->per_user_limit > 0 && ($user->services->where('product_id', $item->product->id)->count() >= $item->product->per_user_limit ||
                        $cart->items->filter(fn ($it) => $it->product->id == $item->product->id)->sum(fn ($it) => $it->quantity) + $user->services->where('product_id', $item->product->id)->count() > $item->product->per_user_limit
                    )
                ) {
                    throw new DisplayException(__('product.user_limit', ['product' => $item->product->name]));
                }
                if ($item->product->stock !== null) {
                    if ($item->product->stock < $item->quantity) {
                        throw new DisplayException(__('product.out_of_stock', ['product' => $item->product->name]));
                    }

                    $item->product->stock -= $item->quantity;
                    $item->product->save();
                }
            }
            // Create the order
            $order = new Order([
                'user_id' => $user->id,
                'currency_code' => $cart->currency_code,
            ]);
            $order->save();

            // Create the invoice
            if ($this->total->price > 0) {
                $invoice = new Invoice([
                    'user_id' => $user->id,
                    'due_at' => now()->addDays(7),
                    'currency_code' => $cart->currency_code,
                ]);
                $invoice->save();
            }

            // Create the services
            foreach ($cart->items as $item) {
                // Is it a lifetime coupon, then we can adjust the price of the service
                if ($this->coupon && ($this->coupon->recurring === null || (int) $this->coupon->recurring == 1)) {
                    // Apply coupon only to first billing cycle (use original price for recurring)
                    $price = $item->price->original_price;
                } else {
                    // Apply coupon to all billing cycles (use discounted price)
                    $price = $item->price->price;
                }
                // Create the service
                $service = $order->services()->create([
                    'user_id' => $user->id,
                    'currency_code' => $cart->currency_code,
                    'product_id' => $item->product->id,
                    'plan_id' => $item->plan->id,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'coupon_id' => $cart->coupon_id,
                ]);

                foreach ($item->checkout_config as $key => $value) {
                    $service->properties()->updateOrCreate([
                        'key' => $key,
                    ], [
                        'value' => $value,
                    ]);
                }

                foreach ($item->config_options as $configOption) {
                    $configOption = (object) $configOption;
                    if (in_array($configOption->option_type, ['text', 'number'])) {
                        if (!isset($configOption->value)) {
                            continue;
                        }
                        $service->properties()->updateOrCreate([
                            'key' => $configOption->option_env_variable ? $configOption->option_env_variable : $configOption->option_name,
                        ], [
                            'name' => $configOption->option_name,
                            'value' => $configOption->value,
                        ]);

                        continue;
                    }
                    if (!isset($configOption->value) || $configOption->value === null) {
                        continue;
                    }

                    $service->configs()->create([
                        'config_option_id' => $configOption->option_id,
                        'config_value_id' => $configOption->value,
                    ]);
                }

                // Create the invoice items
                if ($item->price->total > 0) {
                    $invoice->items()->create([
                        'reference_id' => $service->id,
                        'reference_type' => Service::class,
                        'price' => $item->price->total,
                        'quantity' => $item->quantity,
                        'description' => $service->description,
                    ]);
                } else {
                    // We'll make the service active immediately
                    if ($service->product->server) {
                        CreateJob::dispatch($service);
                    }
                    $service->status = Service::STATUS_ACTIVE;
                    $service->expires_at = $service->calculateNextDueDate();
                    $service->save();
                }
            }

            // Commit the transaction
            DB::commit();

            // Clear the cart
            ClassesCart::clear();

            if ($this->total->price == 0) {
                // Is it only one item? Then redirect to the service page
                if ($order->services->count() == 1) {
                    return $this->redirect(route('services.show', $order->services->first()), true);
                }

                return $this->redirect(route('services'), true);
            } else {
                return $this->redirect(route('invoices.show', [$invoice, 'pay' => true]), true);
            }
        } catch (Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Return error message
            // Is it a real error or a validation error?
            // If it's a validation error, you can use the $this->addError() method to display the error message to the user.
            if ($e instanceof DisplayException) {
                return $this->notify($e->getMessage(), 'error');
            } else {
                report($e);
                $this->notify('An error occurred while processing your order. Please try again later.');
            }
        }
    }

    public function render()
    {
        return view('cart');
    }
}
