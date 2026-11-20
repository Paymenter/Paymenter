<?php

namespace App\Models;

/**
 * @property int $tld_id
 * @property string $currency_code
 * @property int $years
 * @property float|null $cost
 * @property float|null $register
 * @property float|null $renew
 * @property float|null $transfer
 * @property-read Currency|null $currency
 */
class TldPrice extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tld_id',
        'currency_code',
        'years',
        'cost',
        'register',
        'renew',
        'transfer',
    ];

    protected $casts = [
        'years' => 'integer',
        'cost' => 'float',
        'register' => 'float',
        'renew' => 'float',
        'transfer' => 'float',
    ];

    public function tld()
    {
        return $this->belongsTo(Tld::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }
}
