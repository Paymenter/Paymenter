<?php

namespace App\Livewire;

use App\Classes\Cart as ClassesCart;
use App\Classes\Price;
use App\Exceptions\DisplayException;
use App\Helpers\ExtensionHelper;
use App\Models\Coupon;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;

class Cart extends Component
{
    #[Locked]
    public $items;

    #[Locked]
    public $total;

    #[Locked]
    public array $gateways;

    public $gateway;

    public $coupon;

    public function mount()
    {
        if (Session::has('coupon')) {
            $this->coupon = Session::get('coupon');
        }
        $this->items = ClassesCart::get();
        $this->updateTotal();
    }

    private function updateTotal()
    {
        if ($this->items->isEmpty()) {
            $this->total = null;

            return;
        }
        $this->total = new Price(['price' => $this->items->sum(fn ($item) => $item->price->price * $item->quantity), 'currency' => $this->items->first()->price->currency]);
        $this->gateways = ExtensionHelper::getCheckoutGateways($this->items, 'cart');
        if (count($this->gateways) > 0 && !array_search($this->gateway, array_column($this->gateways, 'id')) !== false) {
            $this->gateway = $this->gateways[0]->id;
        }
    }

    public function applyCoupon()
    {
        $coupon = Coupon::where('code', $this->coupon)->first();
        if (!$coupon) {
            $this->notify('Invalid coupon code', 'error');

            return;
        }
        if ($coupon->max_uses && $coupon->orders->count() >= $coupon->max_uses) {
            return $this->notify('Coupon code has reached its maximum uses', 'error');
        }
        Session::put(['coupon' => $coupon]);
        $this->coupon = $coupon;
        $wasSuccessful = false;
        $this->items = ClassesCart::get()->map(function ($item) use ($coupon, &$wasSuccessful) {
            if ($coupon->products->where('id', $item->product->id)->isEmpty()) {
                return (object) $item;
            }
            $wasSuccessful = true;
            $discount = 0;
            if ($coupon->type === 'percentage') {
                $discount = $item->price->price * $coupon->value / 100;
            } elseif ($coupon->type === 'fixed') {
                $discount = $coupon->value;
            } else {
                $discount = $item->price->setup_fee;
                $item->price->setup_fee = 0;
            }
            if ($item->price->price < $discount) {
                $discount = $item->price->price;
            }
            $item->price->setDiscount($discount);
            $item->price->price -= $discount;

            return (object) $item;
        });
        $this->updateTotal();
        if ($wasSuccessful) {
            $this->notify('Coupon code applied successfully', 'success');
        } else {
            $this->notify('Coupon code does not apply to any of the products in your cart', 'error');
        }
    }

    public function removeCoupon()
    {
        Session::forget('coupon');
        $this->items = ClassesCart::get()->map(function ($item) {
            $item->price->setup_fee = $item->price->original_setup_fee;
            $item->price->price = $item->price->original_price;

            $item->price->setDiscount(0);

            return (object) $item;
        });
        $this->coupon = null;
        $this->updateTotal();
        $this->notify('Coupon code removed successfully', 'success');
    }

    public function removeProduct($index)
    {
        ClassesCart::remove($index);
        $this->items = ClassesCart::get()->map(function ($item) {
            return (object) $item;
        });
        $this->updateTotal();
    }

    public function updateQuantity($index, $quantity)
    {
        if ($this->items->get($index)->product->allow_quantity !== 'combined') {
            return;
        }
        if ($quantity < 1) {
            $this->removeProduct($index);

            return;
        }
        $this->items->get($index)->quantity = $quantity;
        session(['cart' => $this->items->toArray()]);
        $this->updateTotal();
    }

    // Checkout
    public function checkout()
    {
        if ($this->items->isEmpty()) {
            return;
        }
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        //Start database transaction
        DB::beginTransaction();
        try {
            $user = User::where('id', Auth::id())->lockForUpdate()->first();
            // Lock the orderproducts
            foreach ($this->items as $item) {
                if (
                    $item->product->per_user_limit > 0 && ($user->services->where('product_id', $item->product->id)->count() >= $item->product->per_user_limit ||
                        $this->items->filter(fn ($it) => $it->product->id == $item->product->id)->sum(fn ($it) => $it->quantity) + $user->services->where('product_id', $item->product->id)->count() > $item->product->per_user_limit
                    )
                ) {
                    throw new DisplayException(__('product.user_limit', ['product' => $item->product->name]));
                }
                if ($item->product->stock) {
                    if ($item->product->stock < $item->quantity) {
                        throw new DisplayException(__('product.out_of_stock', ['product' => $item->product->name]));
                    }

                    $item->product->stock -= $item->quantity;
                    $item->product->save();
                }
            }
            // Create the order
            $order = $user->orders()->create([
                'currency_code' => $this->total->currency->code,
                'coupon_id' => Session::has('coupon') ? Session::get('coupon')->id : null,
            ]);

            // Create the invoice
            if ($this->total->price > 0) {
                $invoice = Invoice::create([
                    'user_id' => $user->id,
                    'issued_at' => now(),
                    'due_at' => now()->addDays(7),
                    'currency_code' => $this->total->currency->code,
                ]);
            }

            // Create the services
            foreach ($this->items as $item) {
                // Is it a lifetime coupon, then we can adjust the price of the service
                if ($this->coupon && $this->coupon->time === 'lifetime') {
                    $price = $item->price->price - $item->price->setup_fee;
                } else {
                    $price = $item->price->original_price - $item->price->original_setup_fee;
                }
                // Create the service
                $service = $order->services()->create([
                    'product_id' => $item->product->id,
                    'plan_id' => $item->plan->id,
                    'price' => $price,
                    'quantity' => $item->quantity,
                ]);

                foreach ($item->configOptions as $configOption) {
                    if (in_array($configOption->option_type, ['text', 'number'])) {
                        $service->properties()->updateOrCreate([
                            'key' => $configOption->option_env_variable,
                        ], [
                            'name' => $configOption->option_name,
                            'value' => $configOption->value,
                        ]);

                        continue;
                    }

                    $service->configs()->create([
                        'config_option_id' => $configOption->option_id,
                        'config_value_id' => $configOption->value,
                    ]);
                }

                // Create the invoice items
                if ($item->price->price > 0) {
                    $invoice->items()->create([
                        'reference_id' => $service->id,
                        'reference_type' => Service::class,
                        'price' => $item->price->price,
                        'quantity' => $item->quantity,
                        'description' => $service->description,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Clear the cart
            Session::forget('cart');
            Session::forget('coupon');

            // Pass the gateway to the payment page
            Session::put(['gateway' => $this->gateway]);

            if ($this->total->price == 0) {
                // Fixme: Redirect to the order page
                return $this->notify('Order placed successfully', 'success');
            } else {
                return $this->redirect(route('invoices.show', $invoice) . '?gateway=' . $this->gateway . '&pay', true);
            }
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            // Return error message
            // Is it a real error or a validation error?
            // If it's a validation error, you can use the $this->addError() method to display the error message to the user.
            if ($e instanceof DisplayException) {
                $this->notify($e->getMessage(), 'error');
            } else {
                Log::error($e);
                $this->notify('An error occurred while processing your order. Please try again later.');
            }
        }
    }

    public function render()
    {
        return view('cart');
    }
}
