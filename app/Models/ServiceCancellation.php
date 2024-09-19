<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
