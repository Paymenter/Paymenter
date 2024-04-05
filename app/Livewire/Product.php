<?php

namespace App\Livewire;

use App\Models\Product as ModelsProduct;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Product extends Component
{
    public ModelsProduct $product;
    public $added = false;

    public function mount(ModelsProduct $product)
    {
        $this->product = $this->product;
    }
    

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        $key = array_search($this->product->id, array_column($cart, 'product_id'));

        if (!$this->product->allow_quantity && $cart && $key !== false) {
            return redirect()->route('checkout.index')->with('error', 'You already have this product in your shopping cart');
        }
        if ($this->product->stock_enabled && $this->product->stock <= 0) {
            return session()->flash('error', 'Product is out of stock');
            return $this->redirect(request()->headers->get('referer'));
        }
        if ($this->product->limit) {
            $orderProducts = 0;
            if (auth()->check() && auth()->user()->orderProducts) {
                $orderProducts = Auth::user()->orderProducts()->where('product_id', $this->product->id)->count();
                if ($orderProducts >= $this->product->limit) {
                    session()->flash('error', 'Product limit reached');
                    return $this->redirect(request()->headers->get('referer'));
                }
            }
            if ($key !== false && $cart[$key]['quantity'] + $orderProducts >= $this->product->limit) {
                session()->flash('error', 'Product limit reached');
                return $this->redirect(request()->headers->get('referer'));
            }
        }
        if (isset($this->product->extension_id) && $this->product->extension) {
            $server = $this->product->extension;
            $module = "App\\Extensions\\Servers\\" . $server->name . "\\" . $server->name;
            if (class_exists($module)) {
                $module = new $module($server);
                if (method_exists($module, 'getUserConfig')) {
                    return redirect()->route('checkout.config', $this->product->id);
                }
            }
        }
        if ($this->product->prices()->get()->first()->type == 'recurring' || count($this->product->configurableGroups()) > 0) {
            return redirect()->route('checkout.config', $this->product->id);
        }

        $this->product->quantity = 1;
        if ($key !== false && $cart[$key]){
            if ($this->product->stock_enabled && $this->product->stock <= $cart->firstWhere('product_id', $this->product->id)['quantity']) {
                return redirect()->back()->with('error', 'Product is out of stock');
            }
            if ($this->product->quantity != 0) {
                $cart[$key]['quantity'] = $cart[$key]['quantity'] + 1;
            }

            session()->put('cart', $cart);

            $this->added = true;
            return $this->dispatch('updateCart');
        }
        $this->product->price = $this->product->prices()->get()->first()->type == 'one-time' ? $this->product->prices()->get()->first()->monthly : 0;
        $cart[] = [
            'product_id' => $this->product->id,
            'price' => $this->product->price,
            'quantity' => 1,
        ];
        session()->put('cart', $cart);
        $this->added = true;
        return $this->dispatch('updateCart');
    }

    public function render()
    {
        return view('livewire.product');
    }
}
