<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'product',
        'expiry_date',
        'status',
        'user',
    ];
/*
    public function product()
    {
        return $this->belongsTo(Products::class, 'product');
    }*/

    public function client()
    {
        return $this->belongsTo(User::class, 'id', 'user');
    }
}
