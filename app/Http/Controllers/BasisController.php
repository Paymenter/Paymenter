<?php

namespace App\Http\Controllers;

use App\Models\Services;
use App\Models\Categories;

class BasisController extends Controller
{
    function index()
    {
        //$services = Services::where('client', auth()->user()->id)->get();
        $categories = Categories::all();
        return view('welcome', compact('categories'));
    }
}
