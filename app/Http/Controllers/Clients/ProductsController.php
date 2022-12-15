<?php
namespace App\Http\Controllers\Clients;


use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Invoices;
use App\Models\OrderProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProductsController extends Controller
{
    function view(OrderProducts $product)
    {
        //wip
        return view('clients.products.view', compact('product'));
    }
}