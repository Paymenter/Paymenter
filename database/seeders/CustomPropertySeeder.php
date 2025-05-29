<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomPropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed custom user properties
        // for phone, company_name, country, address, address2, city, state, zip
        DB::table('custom_properties')->insertOrIgnore([
            [
                'key' => 'phone',
                'name' => 'Phone',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 1,
                'show_on_invoice' => 0,
                'validation' => 'string|max:255',
            ],
            [
                'key' => 'company_name',
                'name' => 'Company Name',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 0,
                'show_on_invoice' => 1,
                'validation' => 'string|max:255',
            ],
            [
                'key' => 'address',
                'name' => 'Address',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 1,
                'show_on_invoice' => 1,
                'validation' => 'string|max:255',
            ],
            [
                'key' => 'address2',
                'name' => 'Address 2',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 0,
                'show_on_invoice' => 0,
                'validation' => 'string|max:255',
            ],
            [
                'key' => 'city',
                'name' => 'City',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 1,
                'show_on_invoice' => 1,
                'validation' => 'string|max:255',
            ],
            [
                'key' => 'state',
                'name' => 'State',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 1,
                'show_on_invoice' => 1,
                'validation' => 'string|max:255',
            ],
            [
                'key' => 'zip',
                'name' => 'ZIP',
                'model' => 'App\Models\User',
                'type' => 'string',
                'non_editable' => 0,
                'required' => 1,
                'show_on_invoice' => 1,
                'validation' => 'string|max:255',
            ],
        ]);

        DB::table('custom_properties')->insertOrIgnore([
            [
                'key' => 'country',
                'name' => 'Country',
                'model' => 'App\Models\User',
                'type' => 'select',
                'non_editable' => 0,
                'required' => 1,
                'show_on_invoice' => 1,
                'allowed_values' => json_encode(array_values(array_slice(config('app.countries', []), 1))),
                'validation' => 'string|max:255',
            ],
        ]);
    }
}
