<?php

namespace App\Admin\Resources\CategoryResource\Pages;

use App\Admin\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
