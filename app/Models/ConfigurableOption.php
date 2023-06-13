<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfigurableOption extends Model
{
    use HasFactory;
    protected $table = 'configurable_options';

    protected $fillable = [
        'name',
        'type',
        'order',
        'hidden',
    ];

    public function configurableGroup()
    {
        return $this->belongsTo(ConfigurableGroup::class, 'group_id', 'id');
    }

    public function configurableOptionInputs()
    {
        return $this->hasMany(ConfigurableOptionInput::class, 'option_id', 'id');
    }
}