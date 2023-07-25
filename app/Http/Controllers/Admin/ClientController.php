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
        $users = User::all();

        return view('admin.clients.index', compact('users'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
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
        if (auth()->user()->id == $user->id) {
            return redirect()->back()->with('error', 'You cannot edit your own account');
        }
        if (auth()->user()->permissions) {
            return redirect()->back()->with('error', 'Only Admins with full permissions can edit users');
        }
        $user->update($request->all());
        $user->role_id = $request->input('role');
        $user->save();
        return redirect()->route('admin.clients.edit', $user->id)->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
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
            $orderProduct = $user->orders()->first();
            if (!$orderProduct) {
                return redirect()->route('admin.clients.edit', $user->id)->with('error', 'No orders found');
            }
            $orderProduct = $orderProduct->products()->first();
            if (!$orderProduct) {
                return redirect()->route('admin.clients.edit', $user->id)->with('error', 'No orders found');
            }
        }
        $orderProducts = $user->orderProducts;
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

    /**
     * Create/Suspend/Unsuspend/Terminate the order product
     * 
     * @return Redirect
     */
    public function changeProductStatus(User $user, OrderProduct $orderProduct, Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:create,suspend,unsuspend,terminate',
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
            'value' => 'required',
        ]);

        $orderProductConfig->value = $request->input('value');
        $orderProductConfig->save();

        return redirect()->route('admin.clients.products', [$user->id, $orderProduct->id])->with('success', 'Product updated');
    }
}
