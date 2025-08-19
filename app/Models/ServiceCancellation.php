<?php

namespace App\Models;

use App\Observers\ServiceCancellationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([ServiceCancellationObserver::class])]
class ServiceCancellation extends Model implements Auditable
{
    use HasFactory, \App\Models\Traits\Auditable;

    protected $fillable = [
        'service_id',
        'reason',
        'type',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
