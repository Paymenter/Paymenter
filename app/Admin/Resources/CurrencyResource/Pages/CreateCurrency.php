<?php

namespace App\Admin\Resources\CurrencyResource\Pages;

use App\Admin\Resources\CurrencyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;
}
