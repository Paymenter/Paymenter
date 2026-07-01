<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class ProductLocationOffering extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'price_delta' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function providerLocationOffering(): BelongsTo
    {
        return $this->belongsTo(ProviderLocationOffering::class);
    }
}
