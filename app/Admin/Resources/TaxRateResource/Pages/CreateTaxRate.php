<?php

namespace App\Admin\Resources\TaxRateResource\Pages;

use App\Admin\Resources\TaxRateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxRate extends CreateRecord
{
    protected static string $resource = TaxRateResource::class;
}
