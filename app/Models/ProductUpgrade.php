<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUpgrade extends Model
{
    use HasFactory;

    public $fillable = [
        'product_id',
        'upgrade_product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function upgradeProduct()
    {
        return $this->belongsTo(Product::class, 'upgrade_product_id');
    }
}
