<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;

    protected $table = 'product_price';

    protected $fillable = [
        'product_id',
        'type',
        'monthly',
        'quarterly',
        'semi_annually',
        'annually',
        'biennially',
        'triennially',
        'monthly_setup',
        'quarterly_setup',
        'semi_annually_setup',
        'annually_setup',
        'biennially_setup',
        'triennially_setup',
    ];

    public function getProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
