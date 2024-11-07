<?php

namespace Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages;

use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
