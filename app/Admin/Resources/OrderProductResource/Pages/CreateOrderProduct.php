<?php

namespace App\Admin\Resources\OrderProductResource\Pages;

use App\Admin\Resources\OrderProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderProduct extends CreateRecord
{
    protected static string $resource = OrderProductResource::class;
}
