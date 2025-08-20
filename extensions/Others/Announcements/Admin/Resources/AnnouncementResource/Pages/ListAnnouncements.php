<?php

namespace Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
