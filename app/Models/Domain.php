<?php

namespace App\Models;

use App\Enums\DomainStatus;
use App\Models\Traits\Auditable;
use App\Observers\DomainObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * @property-read Service|null $service
 * @property-read Server|null $server
 * @property-read Tld|null $tldRecord
 */
#[ObservedBy([DomainObserver::class])]
class Domain extends Model implements AuditableContract
{
    use Auditable;

    protected $fillable = [
        'service_id',
        'server_id',
        'tld_id',
        'name',
        'tld',
        'registrar',
        'registrar_ref',
        'status',
        'registered_at',
        'expires_at',
        'grace_until',
        'redemption_until',
        'auto_renew',
        'locked',
        'privacy',
        'nameservers',
        'contacts',
        'epp_code',
        'transfer_status',
        'sync_error',
        'last_transition_reason',
        'last_synced_at',
    ];

    protected $casts = [
        'status' => DomainStatus::class,
        'registered_at' => 'date',
        'expires_at' => 'date',
        'grace_until' => 'date',
        'redemption_until' => 'date',
        'auto_renew' => 'boolean',
        'locked' => 'boolean',
        'privacy' => 'boolean',
        'nameservers' => 'array',
        'contacts' => 'encrypted:array',
        'epp_code' => 'encrypted',
        'last_synced_at' => 'datetime',
    ];

    protected $hidden = [
        'epp_code',
        'contacts',
    ];

    protected $auditExclude = [
        'epp_code',
        'contacts',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function tldRecord(): BelongsTo
    {
        return $this->belongsTo(Tld::class, 'tld_id');
    }

    public function years(): int
    {
        $service = $this->service;

        if (!$service) {
            return 1;
        }

        $years = $service->properties->where('key', 'years')->value('value')
            ?? $service->quantity
            ?? 1;

        return max(1, (int) $years);
    }
}
