<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigOptionProduct extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $fillable = [
        'config_option_id',
        'product_id',
    ];

    public $timestamps = false;

    /**
     * Get the option of the product.
     */
    public function configOption()
    {
        return $this->belongsTo(ConfigOption::class);
    }

    /**
     * Get the product of the option.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
