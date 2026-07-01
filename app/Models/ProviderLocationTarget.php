<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class ProviderLocationTarget extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_DISABLED = 'disabled';

    protected $guarded = [];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function providerLocationOffering(): BelongsTo
    {
        return $this->belongsTo(ProviderLocationOffering::class);
    }
}
