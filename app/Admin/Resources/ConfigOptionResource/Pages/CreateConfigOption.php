<?php

namespace App\Admin\Resources\ConfigOptionResource\Pages;

use App\Admin\Resources\ConfigOptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConfigOption extends CreateRecord
{
    protected static string $resource = ConfigOptionResource::class;
}
