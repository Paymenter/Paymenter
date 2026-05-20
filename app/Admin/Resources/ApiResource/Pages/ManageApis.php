<?php

namespace App\Admin\Resources\ApiResource\Pages;

use App\Admin\Resources\ApiResource;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageApis extends ManageRecords
{
    protected static string $resource = ApiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->mutateDataUsing(function (array $data): array {
                $token = 'PAYM' . bin2hex(random_bytes(32));
                $data['token'] = hash('sha256', $token);
                $data['type'] = 'admin';

                Notification::make()
                    ->title(__('apis.token_created'))
                    ->body(Str::markdown(__('apis.token_created_body', ['token' => $token])))
                    ->icon('heroicon-o-key')
                    ->success()
                    ->persistent()
                    ->send();

                return $data;
            }),
        ];
    }
}
