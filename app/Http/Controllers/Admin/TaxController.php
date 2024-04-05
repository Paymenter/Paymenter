<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Constants;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $countries = ['all' => 'All Countries'] + Constants::countries();
        $taxrates = TaxRate::all();
        
        return view('admin.tax.index', compact('taxrates', 'countries'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'taxrates' => 'required|array',
            'tax_enabled' => 'required|boolean',
            'tax_type' => 'required|string|in:inclusive,exclusive',
            'taxrate.*.name' => 'required|string|max:255',
            'taxrate.*.rate' => 'required|numeric|min:0|max:100',
            'taxrate.*.country' => 'required|string|in:all,' . implode(',', array_keys(Constants::countries())),
            'taxrate.*.delete' => 'nullable|boolean',
        ]);
        
        $taxrates = $request->taxrates;
        foreach ($taxrates as $taxrate) {
            if (isset($taxrate['delete']) && $taxrate['delete'] == true) {
                TaxRate::where('id', $taxrate['id'])->delete();
            } else {
                TaxRate::where('id', $taxrate['id'])->update([
                    'name' => $taxrate['name'],
                    'rate' => $taxrate['rate'],
                    'country' => $taxrate['country'],
                ]);
            }
        }
        
        Setting::updateOrCreate(['key' => 'tax_enabled'], ['value' => $request->tax_enabled]);
        Setting::updateOrCreate(['key' => 'tax_type'], ['value' => $request->tax_type]);

        return redirect()->route('admin.taxes')->with('success', 'Tax rate updated successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tax_rates',
            'rate' => 'required|numeric|min:0|max:100',
            'country' => 'required|string|in:all,' . implode(',', array_keys(Constants::countries())),
        ]);

        TaxRate::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'country' => $request->country,
        ]);

        return redirect()->route('admin.taxes')->with('success', 'Tax rate created successfully');
    }
}

