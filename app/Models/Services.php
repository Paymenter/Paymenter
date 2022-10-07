<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = [
        'product',
        'expiry_date',
        'status',
    ];
/*
    public function product()
    {
        return $this->belongsTo(Products::class, 'product');
    }*/

    public function client()
    {
        return $this->belongsTo(User::class, 'client');
    }
}
