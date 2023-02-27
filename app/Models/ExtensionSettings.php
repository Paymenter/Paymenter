<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $this->belongsTo(Extension::class, 'id', 'extension');
    }
}
