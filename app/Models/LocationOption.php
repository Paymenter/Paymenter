<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class LocationOption extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    public const TYPE_GEO = 'geo';

    public const TYPE_REGION = 'region';

    public const TYPE_SYNTHETIC_POOL = 'synthetic_pool';

    public const TYPE_ISP_POOL = 'isp_pool';

    public const TYPE_NETWORK_POOL = 'network_pool';

    public const TYPE_PROMO_POOL = 'promo_pool';

    public const TYPE_UNKNOWN = 'unknown';

    public const NETWORK_DATACENTER = 'datacenter';

    public const NETWORK_RESIDENTIAL = 'residential';

    public const NETWORK_MOBILE = 'mobile';

    public const NETWORK_VPN = 'vpn';

    public const NETWORK_MIXED = 'mixed';

    public const POLICY_FIXED = 'fixed';

    public const POLICY_RANDOM = 'random';

    public const POLICY_PROVIDER_DECIDES = 'provider_decides';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_HIDDEN = 'hidden';

    public const STATUS_DEPRECATED = 'deprecated';

    protected $guarded = [];

    protected $casts = [
        'legacy_id' => 'integer',
        'service_types' => 'array',
        'metadata' => 'array',
    ];

    public function primaryGroup(): BelongsTo
    {
        return $this->belongsTo(LocationGroup::class, 'primary_group_id');
    }

    public function providerLocationOfferings(): HasMany
    {
        return $this->hasMany(ProviderLocationOffering::class);
    }
}
