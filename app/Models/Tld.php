<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read Server|null $server
 * @property-read Product|null $product
 */
class Tld extends Model
{
    protected $fillable = [
        'tld',
        'server_id',
        'product_id',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(TldPrice::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
