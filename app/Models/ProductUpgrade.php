<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ProductUpgrade extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function upgrade()
    {
        return $this->belongsTo(Product::class, 'upgrade_id');
    }
}
