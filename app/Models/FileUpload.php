<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class FileUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'fileable_id',
        'fileable_type',
        'filename',
        'mime_type',
        'size',
    ];
    
    public function fileable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        return asset('storage/uploads/' . $this->uuid . '.' . File::extension($this->filename));
    }

    public function isImage()
    {
        return strpos($this->mime_type, 'image') !== false;
    }
}
