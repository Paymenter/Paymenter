<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Extensions extends Model
{
    use HasFactory;
    protected $table = 'extensions';
    protected $fillable = [
        'name',
        'enabled',
        'type',
    ];

    public function getConfig()
    {
        return $this->hasMany(ExtensionSettings::class, 'extension', 'id');
    }

    public function getServer()
    {
        return $this->hasMany(ProductSettings::class, 'extension', 'id');
    }
}
