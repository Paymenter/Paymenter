<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfigurableOptionInput extends Model
{
    use HasFactory;
    protected $table = 'configurable_option_inputs';
    protected $fillable = [
        'name',
        'order',
        'hidden',
    ];

    public function configurableOption()
    {
        return $this->belongsTo(ConfigurableOption::class, 'configurable_option_id', 'id');
    }

    public function configurableOptionInputPrice()
    {
        return $this->hasOne(ConfigurableOptionInputPrice::class, 'input_id', 'id');
    }
}
