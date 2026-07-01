<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class LocationGroup extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    public const TYPE_GEO = 'geo';

    public const TYPE_REGION = 'region';

    public const TYPE_COUNTRY_BUNDLE = 'country_bundle';

    public const TYPE_ISP_BUNDLE = 'isp_bundle';

    public const TYPE_CUSTOM = 'custom';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_HIDDEN = 'hidden';

    protected $guarded = [];

    protected $casts = [
        'service_types' => 'array',
        'metadata' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LocationGroup::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(LocationGroup::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function options(): HasMany
    {
        return $this->hasMany(LocationOption::class, 'primary_group_id')->orderBy('sort_order')->orderBy('display_name');
    }
}
