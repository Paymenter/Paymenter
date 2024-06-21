<?php

namespace App\Admin\Resources\UserPropertyResource\Pages;

use App\Admin\Resources\UserPropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserProperty extends CreateRecord
{
    protected static string $resource = UserPropertyResource::class;
}
