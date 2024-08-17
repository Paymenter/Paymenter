<?php

namespace App\Admin\Resources\EmailTemplateResource\Pages;

use App\Admin\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;
}
