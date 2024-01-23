<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ExtensionHelper;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Invoice, Order, OrderProduct, OrderProductConfig, Role, Ticket};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;

class ClientController extends Controller
{
    public function index()
    {
        return view('admin.clients.index');
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|min:8',
        ]);

        if ($request->password) {
            $password = Hash::make($request->password);
            $request->merge(['password' => $password]);
        } else {
            $password = Hash::make(\Str::random());
            $request->merge(['password' => $password]);
            $sendPassword = true;
        }
        $user = User::create($request->all());
        isset($sendPassword) && $sendPassword ? Password::sendResetLink(['email' => $user->email]) : null;


        return redirect()->route('admin.clients.edit', $user->id);
    }

    public function edit(User $user)
    {
        $user = $user->load('role');
        $roles = Role::all();
        return view('admin.clients.edit', compact('user', 'roles'));
    }

    public function loginasClient(User $user)
    {
        auth()->login($user);

        return redirect()->route('index');
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->permissions) {
            return redirect()->back()->with('error', 'Only Admins with full permissions can edit users');
        }
        $user->update($request->all());
        if (auth()->user()->id !== $user->id) {
            $user->role_id = $request->input('role');
            $user->save();
        }
        return redirect()->route('admin.clients.edit', $user->id)->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        $user->delete();

        return redirect()->route('admin.clients');
    }


    /**
     * Display the Products
     *
     * @return View
     */
    public function products(User $user, OrderProduct $orderProduct = null)
    {
        if (!$orderProduct) {
            $orderProduct = $user->orderProducts()->with(['product'])->first();
            if (!$orderProduct) {
                return redirect()->route('admin.clients.edit', $user->id)->with('error', 'No orders found');
            }
        }
        $orderProducts = $user->orderProducts()->with('product')->get();
        $configurableOptions = $orderProduct->config;

        return view('admin.clients.products', compact('user', 'orderProducts', 'orderProduct', 'configurableOptions'));
    }

    /**
     * Change the order product
     *
     * @return Redirect
     */
    public function updateProduct(User $user, OrderProduct $orderProduct, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'expiry_date' => 'required|date',
            'status' => 'required|in:pending,paid,cancelled,suspended',
        ]);

        $orderProduct->price = $request->input('price');
        $orderProduct->quantity = $request->input('quantity');
        $orderProduct->expiry_date = $request->input('expiry_date');
        $orderProduct->status = $request->input('status');
        $orderProduct->save();

        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Product updated');
    }

    public function removeCancellation(User $user, OrderProduct $orderProduct): \Illuminate\Http\RedirectResponse
    {
        if(!$orderProduct->cancellation()->exists())
            return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('error', 'No cancellation found');

        $orderProduct->cancellation()->delete();

        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Cancellation removed');
    }

    /**
     * Create/Suspend/Unsuspend/Terminate the order product
     *
     * @return Redirect
     */
    public function changeProductStatus(User $user, OrderProduct $orderProduct, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:create,suspend,unsuspend,terminate,upgrade',
        ]);

        switch ($request->input('status')) {
            case 'create':
                ExtensionHelper::createServer($orderProduct);
                break;
            case 'suspend':
                ExtensionHelper::suspendServer($orderProduct);
                break;
            case 'unsuspend':
                ExtensionHelper::unsuspendServer($orderProduct);
                break;
            case 'terminate':
                ExtensionHelper::terminateServer($orderProduct);
                break;
            case 'upgrade':
                ExtensionHelper::upgradeServer($orderProduct);
                break;
        }


        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Product status changed');
    }

    /**
     * Update the product configurable options
     *
     * @return Redirect
     */
    public function updateProductConfig(User $user, OrderProduct $orderProduct, OrderProductConfig $orderProductConfig, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'key' => 'sometimes',
            'value' => 'required',
        ]);

        $orderProductConfig->value = $request->input('value');
        if(!$orderProductConfig->configurableOption() || !$orderProductConfig->configurableOption()->exists())
            $orderProductConfig->key = $request->input('key');
        $orderProductConfig->save();

        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Product updated');
    }

    /**
     * Add a product configurable option
     * 
     * @return Redirect
     */
    public function newProductConfig(User $user, OrderProduct $orderProduct, Request $request): \Illuminate\Http\RedirectResponse
    {
        $orderProductConfig = new OrderProductConfig();
        $orderProductConfig->key = 'New Config';
        $orderProductConfig->value = 'New Value';
        $orderProductConfig->order_product_id = $orderProduct->id;
        $orderProductConfig->save();

        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Product updated');
    }

    /**
     * Delete a product configurable option
     * 
     * @return Redirect
     */
    public function deleteProductConfig(User $user, OrderProduct $orderProduct, OrderProductConfig $orderProductConfig): \Illuminate\Http\RedirectResponse
    {
        $orderProductConfig->delete();

        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Product updated');
    }
}
