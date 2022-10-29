<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'products',
        'expiry_date',
        'status',
        'client',
        'total'
    ];
    protected $casts = [
        'products' => 'array',
    ];
/*
    public function product()
    {
        return $this->belongsTo(Products::class, 'product');
    }*/

    public function client()
    {
        return $this->belongsTo(User::class, 'client', 'id');
    }
}
