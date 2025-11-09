<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Clusters;

use Filament\Clusters\Cluster;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource;

class Knowledgebase extends Cluster
{
    protected static ?string $slug = 'knowledgebase';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-book-open-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-book-open-fill';

    protected static ?string $navigationLabel = 'Knowledgebase';

    public static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 0;

    public static function getNavigationUrl(): string
    {
        return KnowledgeCategoryResource::getUrl();
    }
}
