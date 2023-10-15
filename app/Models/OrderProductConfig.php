<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProductConfig extends Model
{
    use HasFactory;
    protected $table = 'order_products_config';
    protected $fillable = [
        'order_product_id',
        'is_configurable_option',
        'key',
        'value',
    ];

    public function product()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id', 'id');
    }

    /**
     * Get the configurable option that owns the OrderProductConfig
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configurableOption()
    {
        if (!$this->is_configurable_option)
            return null;
        return $this->belongsTo(ConfigurableOption::class, 'key', 'id');
    }

    /**
     * Get the configurable option input that owns the OrderProductConfig
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function configurableOptionInput()
    {
        if (!$this->is_configurable_option)
            return null;
        return $this->belongsTo(ConfigurableOptionInput::class, 'value', 'id');
    }
}
