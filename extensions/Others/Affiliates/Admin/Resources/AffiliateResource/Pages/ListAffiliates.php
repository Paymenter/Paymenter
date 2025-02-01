<?php

namespace Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource;

class ListAffiliates extends ListRecords
{
    protected static string $resource = AffiliateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
