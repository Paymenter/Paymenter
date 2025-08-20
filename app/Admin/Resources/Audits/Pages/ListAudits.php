<?php

namespace App\Admin\Resources\Audits\Pages;

use App\Admin\Resources\Audits\AuditResource;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;
}
