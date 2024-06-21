<?php

namespace App\Admin\Resources\RoleResource\Pages;

use App\Admin\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
