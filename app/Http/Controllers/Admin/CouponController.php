<?php
namespace App\Http\Controllers\Admin;

use App\Models\Coupons;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Products;

class CouponController extends Controller
{
    public function index(){
        $coupons = Coupons::all();
        return view('admin.coupon.index', compact('coupons'));
    }
    
    public function create(){
        $products = Products::all();
        return view('admin.coupon.create', compact('products'));
    }

    public function store(Request $request){
        $request->validate([
            'code' => 'required|unique:coupons',
            'value' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
        ]);

        $coupon = Coupons::create($request->all());

        return redirect()->route('admin.coupon.edit', $coupon->id)->with('success', 'Coupon created successfully!');
    }

    public function edit(Coupons $coupon){
        $products = Products::all();


        return view('admin.coupon.edit', compact('coupon', 'products'));
    }

    public function update(Request $request, Coupons $coupon){
        $request->validate([
            'code' => 'required|unique:coupons,code,'.$coupon->id,
            'value' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
        ]);

        $coupon->update($request->all());

        return redirect()->route('admin.coupon.edit', $coupon->id)->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Coupons $coupon){
        $coupon->delete();
        return redirect()->route('admin.coupon.index')->with('success', 'Coupon deleted successfully!');
    }
}