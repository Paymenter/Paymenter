<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Extension extends Model
{
    use HasFactory;
    protected $table = 'extensions';
    protected $fillable = [
        'name',
        'enabled',
        'type',
        'display_name',
    ];

    public function getConfig()
    {
        return $this->hasMany(ExtensionSetting::class);
    }

    public function getServer()
    {
        return $this->hasMany(ProductSetting::class, 'extension', 'id');
    }

    public function getPathAttribute()
    {
        return app_path('Extensions/' . ucfirst($this->type) . 's' . '/' . $this->name);
    }

    public function getNamespaceAttribute()
    {
        return 'App\\Extensions\\' . ucfirst($this->type) . 's' . '\\' . $this->name;
    }
}
