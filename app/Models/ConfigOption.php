<?php

namespace App\Models;

use App\Models\Traits\HasPlans;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigOption extends Model
{
    use HasFactory, HasPlans;

    protected $fillable = [
        'name',
        'env_variable',
        'type',
        'sort',
        'hidden',
        'parent_id',
    ];

    /**
     * Get the parent option.
     */
    public function parent()
    {
        return $this->belongsTo(ConfigOption::class, 'parent_id');
    }

    /**
     * Get the options that belong to the parent.
     */
    public function children()
    {
        return $this->hasMany(ConfigOption::class, 'parent_id');
    }
}
