<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons
     */
    public function index(): View
    {
        return view('admin.coupons.index');
    }

    /**
     * Display the create form
     */
    public function create(): View
    {
        $products = Product::all();

        return view('admin.coupons.create', compact('products'));
    }

    /**
     * Store a coupon
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedRequest = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code',
            'value' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
            'time' => 'required|in:lifetime,onetime',
            'products' => 'nullable',
            'max_uses' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $coupon = Coupon::create($validatedRequest->validated());

        return redirect()->route('admin.coupons.edit', $coupon->id)->with('success', 'Coupon created successfully!');
    }

    /**
     * Display the edit form
     */
    public function edit(Coupon $coupon): View
    {
        $products = Product::all();

        return view('admin.coupons.edit', compact('coupon', 'products'));
    }

    /**
     * Update the coupon
     */
    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validatedRequest = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code,'.$coupon->id,
            'value' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
            'time' => 'required|in:lifetime,onetime',
            'products' => 'nullable',
            'max_uses' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $coupon->update($validatedRequest->validated());

        if (! $request->has('products')) {
            $coupon->products = [];
            $coupon->save();
        }

        return redirect()->route('admin.coupons.edit', $coupon->id)->with('success', 'Coupon updated successfully!');
    }

    /**
     * Delete the coupon
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully!');
    }
}
