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
        'update_available'
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
        return 'App\\Extensions\\' . ucfirst($this->type) . 's' . '\\' . $this->name . '\\' . $this->name;
    }

    public function getVersionAttribute()
    {
        $module = $this->namespace;
        try {
            $module = new $module($this);
            return $module->getMetadata()['version'];
        } catch (\Throwable $th) {
            return false;
        }
    }
}
