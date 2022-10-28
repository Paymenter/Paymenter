<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description', 
        'price', 
        'category_id', 
        'image', 
        'server_id',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function server()
    {
        return $this->belongsTo(Extensions::class, 'server_id');
    }

    public function settings()
    {
        return $this->hasMany(ProductSettings::class, 'product_id');
    }
}
