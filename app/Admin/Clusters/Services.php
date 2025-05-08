<?php

namespace App\Admin\Clusters;

use Filament\Clusters\Cluster;

class Services extends Cluster
{
    protected static ?string $navigationIcon = 'ri-archive-stack-line';

    protected static ?string $activeNavigationIcon = 'ri-archive-stack-fill';

    public static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'services';
}
