<?php
namespace App\Http\Controllers\Clients;

use App\Helpers\ExtensionHelper;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Invoices;
use App\Models\OrderProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProductsController extends Controller
{
    function index(OrderProducts $product)
    {
        if($product->order()->get()->first()->client != Auth::user()->id)
            return abort(404);

        $link = ExtensionHelper::getLink($product);
        
        return view('clients.products.view', compact('product', 'link'));
    }
}