<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'ext_cyberpunk_visits';

    protected $guarded = [];

    // 'date:Y-m-d' evita que se guarde con hora: si la columna guardase
    // "2026-08-02 00:00:00" la búsqueda por "2026-08-02" no encontraría nada
    // y el contador se quedaría siempre a cero.
    protected $casts = [
        'day' => 'date:Y-m-d',
    ];
}
