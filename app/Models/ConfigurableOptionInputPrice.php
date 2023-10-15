<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfigurableOptionInputPrice extends Model
{
    use HasFactory;
    protected $table = 'configurable_option_input_pricing';
    protected $fillable = [
        'monthly',
        'quarterly',
        'semi_annually',
        'annually',
        'biennially',
        'triennially',
        'monthly_setup',
        'quarterly_setup',
        'semi_annually_setup',
        'annually_setup',
        'biennially_setup',
        'triennially_setup',
        'input_id',
    ];

    public function configurableOptionInput()
    {
        return $this->belongsTo(ConfigurableOptionInput::class, 'input_id', 'id');
    }
}
