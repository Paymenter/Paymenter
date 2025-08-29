<?php

namespace App\Admin\Resources\Audits\Pages;

use App\Admin\Resources\Audits\AuditResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;
}
