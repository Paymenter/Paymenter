<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'config_option_id',
        'config_value_id',
    ];

    /**
     * Get the service that owns the service config.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the config option that owns the service config.
     */
    public function configOption()
    {
        return $this->belongsTo(ConfigOption::class);
    }

    /**
     * Get the config value that owns the service config.
     */
    public function configValue()
    {
        return $this->belongsTo(ConfigOption::class, 'config_value_id');
    }
}
