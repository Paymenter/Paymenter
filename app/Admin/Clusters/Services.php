<?php

namespace App\Admin\Clusters;

use Filament\Clusters\Cluster;

class Services extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'services';
}
