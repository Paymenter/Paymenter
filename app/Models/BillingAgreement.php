<?php

namespace App\Models;

use App\Models\Traits\HasProperties;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BillingAgreement extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasProperties, HasUlids, SoftDeletes;

    protected $fillable = [
        'ulid',
        'user_id',
        'gateway_id',
        'name', // e.g. Visa **** 4242
        'external_reference', // e.g. Stripe card id, PayPal billing agreement id
        'type', // e.g. card type
        'expiry',
    ];

    public function uniqueIds()
    {
        return [
            'ulid',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'gateway_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'billing_agreement_id');
    }
}
