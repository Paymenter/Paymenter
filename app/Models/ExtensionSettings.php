<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtensionSettings extends Model
{
    use HasFactory;
    protected $table = 'extension_settings';
    protected $fillable = [
        'extension',
        'key',
        'value',
    ];

    public function extension()
    {
        return $this->belongsTo(Extensions::class, 'id', 'extension');
    }
}
