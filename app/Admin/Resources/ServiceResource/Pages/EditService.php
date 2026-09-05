<?php

namespace App\Admin\Resources\ServiceResource\Pages;

use App\Admin\Actions\AuditAction;
use App\Admin\Resources\ServiceResource;
use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Models\Service;
use App\Support\ServiceAdminAuthorization;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    /**
     * Staff with View Services (view/viewAny) may open this URL. Writes still
     * require Update Services — Filament's default EditRecord gate is update-only.
     */
    protected function authorizeAccess(): void
    {
        abort_unless(ServiceAdminAuthorization::canView(auth()->user(), $this->getRecord()), 403);
    }

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->disabled(!$this->canUpdateRecord());
    }

    protected function getFormActions(): array
    {
        if (!$this->canUpdateRecord()) {
            return [
                $this->getCancelFormAction(),
            ];
        }

        return parent::getFormActions();
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeServiceWrite();

        parent::save($shouldRedirect, $shouldSendSavedNotification);
    }

    public function saveFormComponentOnly(Component $component): void
    {
        $this->authorizeServiceWrite();

        parent::saveFormComponentOnly($component);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $this->authorizeServiceWrite();

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->canUpdateRecord())
                ->form(function (DeleteAction $action) {
                    $status = !in_array($this->record->status, [Service::STATUS_PENDING, Service::STATUS_CANCELLED]) && $this->record->product->server_id !== null;
                    if (!$status) {
                        return [];
                    }

                    return [
                        Checkbox::make('deleteExtensionServer')
                            ->label('Also trigger deletion of server')
                            ->default(true),
                    ];
                })
                ->action(function (array $data, Service $record): void {
                    $this->authorizeServiceWrite();
                    abort_unless(auth()->user()?->can('delete', $record), 403);

                    try {
                        if (($data['deleteExtensionServer'] ?? false)) {
                            ExtensionHelper::terminateServer($record);
                        }
                    } catch (Exception $e) {
                        report($e);

                        Notification::make('Error')
                            ->title('Error occured while deleting the related server:')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                    $record->delete();
                }),
            Action::make('changeStatus')
                ->label('Trigger Extension Action')
                ->visible(fn () => $this->canUpdateRecord())
                ->schema([
                    Select::make('action')
                        ->label('Action')
                        ->options([
                            'create' => 'Create server',
                            'suspend' => 'Suspend server',
                            'unsuspend' => 'Unsuspend server',
                            'terminate' => 'Terminate server',
                            'upgrade' => 'Upgrade server',
                        ])->required(),
                    Checkbox::make('sendNotification')
                        ->label('Send Notification')
                        ->default(false),
                ])
                ->action(function (array $data, Service $record, Action $action): void {
                    $this->authorizeServiceWrite();

                    try {
                        switch ($data['action']) {
                            case 'create':
                                $sdata = ExtensionHelper::createServer($record);
                                if ($data['sendNotification']) {
                                    NotificationHelper::serverCreatedNotification($record->order->user, $record, $sdata);
                                }
                                break;
                            case 'suspend':
                                $sdata = ExtensionHelper::suspendServer($record);
                                break;
                            case 'unsuspend':
                                $sdata = ExtensionHelper::unsuspendServer($record);
                                break;
                            case 'terminate':
                                $sdata = ExtensionHelper::terminateServer($record);
                                break;
                            case 'upgrade':
                                $sdata = ExtensionHelper::upgradeServer($record);
                                break;
                        }
                    } catch (Exception $e) {
                        if (config('app.debug')) {
                            throw $e;
                        }
                        report($e);
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

            AuditAction::make()->auditChildren([
                'order',
                'invoices',
                'properties',
                'configs',
                'invoiceItems',
            ]),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        if (!$this->record->cancellation()->exists()) {
            return [];
        }

        return [
            ServiceResource\Widgets\CancellationOverview::class,
        ];
    }

    protected function canUpdateRecord(): bool
    {
        return ServiceAdminAuthorization::canUpdate(auth()->user(), $this->getRecord());
    }

    protected function authorizeServiceWrite(): void
    {
        ServiceAdminAuthorization::authorizeUpdate(auth()->user(), $this->getRecord());
    }
}
