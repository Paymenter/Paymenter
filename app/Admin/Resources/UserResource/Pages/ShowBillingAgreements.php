<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\GatewayResource;
use App\Admin\Resources\UserResource;
use App\Models\BillingAgreement;
use App\Services\BillingAgreement\RemoveBillingAgreementService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShowBillingAgreements extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'billingAgreements';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-bank-card-line';

    public static function getNavigationLabel(): string
    {
        return 'Billing Agreements';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
                TextColumn::make('external_reference')
                    ->label('External Reference'),
                TextColumn::make('gateway.name')
                    ->url(fn ($record) => GatewayResource::getUrl('edit', ['record' => $record->gateway_id]))
                    ->label('Gateway'),
            ])
            ->recordActions([
                // Not a DeleteAction: removal has to go through the gateway and detach the services
                Action::make('remove')
                    ->label('Remove')
                    ->icon('ri-delete-bin-line')
                    ->color('danger')
                    ->authorize('delete')
                    ->requiresConfirmation()
                    ->modalHeading('Remove billing agreement')
                    ->modalDescription('The gateway will be asked to revoke the stored payment method, and services using it will no longer be charged automatically.')
                    ->action(function (BillingAgreement $record) {
                        if ((new RemoveBillingAgreementService)->handle($record)) {
                            Notification::make()->title('Billing agreement removed')->success()->send();

                            return;
                        }

                        Notification::make()
                            ->title('Billing agreement removed')
                            ->body('The gateway did not confirm that it revoked the payment method. Check the logs for details.')
                            ->warning()
                            ->send();
                    }),
            ]);
    }
}
