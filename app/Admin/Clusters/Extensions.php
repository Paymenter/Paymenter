<?php

namespace App\Admin\Clusters;

use Filament\Clusters\Cluster;

class Extensions extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'ri-puzzle-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-puzzle-fill';

    public static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'extensions';
}
