<?php

namespace App\Admin\Resources\OrderProductResource\Pages;

use App\Admin\Resources\OrderProductResource;
use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditOrderProduct extends EditRecord
{
    protected static string $resource = OrderProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('changeStatus')
                ->label('Trigger Extension Action')
                ->form([
                    Select::make('action')
                        ->label('Action')
                        ->options([
                            'create' => 'Create server',
                            'suspend' => 'Suspend server',
                            'unsuspend' => 'Unsuspend server',
                            'terminate' => 'Terminate server',
                        ])->required(),
                ])
                ->action(function (array $data, OrderProduct $record, Actions\Action $action): void {
                    try {
                        switch ($data['action']) {
                            case 'create':
                                ExtensionHelper::createServer($record);
                                break;
                            case 'suspend':
                                ExtensionHelper::suspendServer($record);
                                break;
                            case 'unsuspend':
                                ExtensionHelper::unsuspendServer($record);
                                break;
                            case 'terminate':
                                ExtensionHelper::terminateServer($record);
                                break;
                        }
                    } catch (\Exception $e) {
                        Notification::make('Error')
                            ->title('Error occured while triggering the action:')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                        $action->halt();
                    }
                    Notification::make('Success')
                        ->title('Action triggered successfully')
                        ->body('The action has been triggered successfully')
                        ->success()
                        ->send();
                })
                ->color('primary')
                ->modalSubmitActionLabel('Trigger'),

        ];
    }
}
