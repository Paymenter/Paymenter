<?php

namespace App\Models;

use App\Observers\ServiceCancellationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ServiceCancellationObserver::class])]
class ServiceCancellation extends Model
{
    use HasFactory;

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
