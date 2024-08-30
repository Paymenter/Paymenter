<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_product_id',
        'config_option_id',
        'config_value_id',
    ];

    /**
     * Get the order product that owns the order product config.
     */
    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the config option that owns the order product config.
     */
    public function configOption()
    {
        return $this->belongsTo(ConfigOption::class);
    }

    /**
     * Get the config value that owns the order product config.
     */
    public function configValue()
    {
        return $this->belongsTo(ConfigOption::class, 'config_value_id');
    }
}
