<?php

namespace App\Models;

use App\Models\Traits\HasPlans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use HasFactory, HasPlans, \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    public $with = ['plans'];

    protected $auditInclude = [
        'name',
        'description',
        'category_id',
        'enabled',
    ];

    /**
     * Get the category of the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the configurable options of the product.
     */
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class)->withPivot('enabled');
    }

    /**
     * Get the extension of the product.
     */
    public function server()
    {
        return $this->belongsTo(Extension::class, 'server_id')->where('type', 'server');
    }

    /**
     * Get the settings of the product.
     */
    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'settingable');
    }
}
