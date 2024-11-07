<?php

namespace Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages;

use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
