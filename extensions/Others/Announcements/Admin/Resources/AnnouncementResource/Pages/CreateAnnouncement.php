<?php

namespace Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource\Pages;

use Paymenter\Extensions\Others\Announcements\Admin\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;
}
