<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;
    protected $table = 'settings';
    protected $fillable = [
        'maintenance',
        'theme',
        'recaptcha',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_twitter_card',
        'advanced_mode',
        'seo_image',
        'currency_sign',
        'currency_position',
        'home_page_text',
        'app_name',
    ];


}
