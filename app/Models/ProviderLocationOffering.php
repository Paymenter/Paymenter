<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class ProviderLocationOffering extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    public const SERVICE_VPS = 'vps';

    public const SERVICE_PROXY = 'proxy';

    public const SERVICE_VPN = 'vpn';

    public const STOCK_UNKNOWN = 'unknown';

    public const STOCK_AVAILABLE = 'available';

    public const STOCK_LIMITED = 'limited';

    public const STOCK_UNAVAILABLE = 'unavailable';

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'capabilities' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'provider_id');
    }

    public function locationOption(): BelongsTo
    {
        return $this->belongsTo(LocationOption::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(ProviderLocationTarget::class)->orderByDesc('priority')->orderByDesc('weight');
    }

    public function productLocationOfferings(): HasMany
    {
        return $this->hasMany(ProductLocationOffering::class);
    }
}
