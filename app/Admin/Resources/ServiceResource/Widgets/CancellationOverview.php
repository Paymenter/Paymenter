<?php

namespace App\Admin\Resources\ServiceResource\Widgets;

use App\Models\Service;
use Filament\Widgets\Widget;

class CancellationOverview extends Widget
{
    protected string $view = 'admin.resources.service-resource.widgets.cancellation-overview';

    protected static bool $isLazy = false;

    public ?Service $record = null;

    protected array|string|int $columnSpan = 'full';
}
