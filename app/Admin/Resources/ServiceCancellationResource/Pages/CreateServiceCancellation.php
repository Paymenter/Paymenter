<?php

namespace App\Admin\Resources\ServiceCancellationResource\Pages;

use App\Admin\Resources\ServiceCancellationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceCancellation extends CreateRecord
{
    protected static string $resource = ServiceCancellationResource::class;
}
