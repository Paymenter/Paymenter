<?php
namespace App\Http\Controllers\Admin;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;

class CouponController extends Controller
{
    public function index(){
        $coupons = Coupon::all();
        return view('admin.coupons.index', compact('coupons'));
    }
    
    public function create(){
        $products = Product::all();
        return view('admin.coupons.create', compact('products'));
    }

    public function store(Request $request){
        $request->validate([
            'code' => 'required|unique:coupons',
            'value' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
            'time' => 'required|in:lifetime,onetime',
        ]);

        $coupon = Coupon::create($request->all());

        return redirect()->route('admin.coupons.edit', $coupon->id)->with('success', 'Coupon created successfully!');
    }

    public function edit(Coupon $coupon){
        $products = Product::all();

        return view('admin.coupons.edit', compact('coupon', 'products'));
    }

    public function update(Request $request, Coupon $coupon){
        $request->validate([
            'code' => 'required|unique:coupons,code,'.$coupon->id,
            'value' => 'required|numeric',
            'type' => 'required|in:percent,fixed',
            'time' => 'required|in:lifetime,onetime',
        ]);

        $coupon->update($request->all());
        if(!$request->has('products')){
            $coupon->products = [];
            $coupon->save();
        }


        return redirect()->route('admin.coupons.edit', $coupon->id)->with('success', 'Coupon updated successfully!');
    }

    public function destroy(Coupon $coupon){
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully!');
    }
}