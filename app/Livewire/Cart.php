<?php

namespace App\Livewire;

use App\Classes\Cart as ClassesCart;
use App\Classes\Price;
use App\Exceptions\DisplayException;
use App\Helpers\ExtensionHelper;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public function mount()
    {
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
        if (!array_search($this->gateway, array_column($this->gateways, 'id')) !== false) {
            $this->gateway = $this->gateways[0]->id;
        }
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
                    $item->product->per_user_limit > 0 && (
                        $user->orderProducts->where('product_id', $item->product->id)->count() >= $item->product->per_user_limit ||
                        $this->items->filter(fn ($it) => $it->product->id == $item->product->id)->sum(fn ($it) => $it->quantity) + $user->orderProducts->where('product_id', $item->product->id)->count() > $item->product->per_user_limit
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

            // Create the order products
            foreach ($this->items as $item) {
                $orderProduct = $order->orderProducts()->create([
                    'product_id' => $item->product->id,
                    'plan_id' => $item->plan->id,
                    'price' => $item->price->price - $item->price->setup_fee,
                    'quantity' => $item->quantity,
                    'expires_at' => now(),
                ]);
                $orderProduct->description;
                foreach ($item->configOptions as $configOption) {
                    $orderProduct->configs()->create([
                        'config_option_id' => $configOption->option_id,
                        'config_value_id' => $configOption->value,
                    ]);
                }

                // Create the invoice items
                if ($item->price->price > 0) {
                    $invoice->items()->create([
                        'order_product_id' => $orderProduct->id,
                        'plan_id' => $item->plan->id,
                        'price' => $item->price->price,
                        'quantity' => $item->quantity,
                        'description' => $orderProduct->description,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Clear the cart
            session(['cart' => []]);

            // Pass the gateway to the payment page
            session(['gateway' => $this->gateway]);

            if ($this->total->price == 0) {
                return $this->redirect(route('invoices.show', $invoice), true);
            } else {
                return $this->redirect(route('invoices.show', $invoice), true);
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
                $this->notify('An error occurred while processing your order. Please try again later.');
            }

            throw $e;
        }
    }

    public function render()
    {
        return view('cart');
    }
}
