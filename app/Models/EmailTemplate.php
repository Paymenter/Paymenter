<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailable',
        'subject',
        'preview_text',
        'html_template',
        'text_template',
    ];

    public function getNameAttribute()
    {
        return class_basename($this->mailable);
    }
}
