<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtensionSetting extends Model
{
    use HasFactory;
    protected $table = 'extension_settings';
    protected $fillable = [
        'extension_id',
        'key',
        'value',
    ];

    public function extension()
    {
        return $this->belongsTo(Extension::class);
    }
}
