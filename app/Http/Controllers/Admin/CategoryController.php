<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index');
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.categories');
    }

    public function show($id)
    {
        return view('admin.categories.show');
    }

    public function edit($id)
    {
        return view('admin.categories.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.categories');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.categories');
    }
}
