<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShowSessions extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'sessions';

    protected static ?string $navigationIcon = 'ri-computer-line';

    public static function getNavigationLabel(): string
    {
        return 'Sessions';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('formatted_device')
                    ->label('Device')
                    ->searchable(),
                TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->last_activity->format('F j, Y, g:i A')),
                TextColumn::make('is_current_device')
                    ->label('Current Device')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->sortable(),
            ])
            ->actions([
                Action::make('logout')
                    ->label('Logout')
                    ->icon('ri-logout-box-r-line')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->delete();
                    })
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Session logged out successfully')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Logout Selected Sessions')
                        ->icon('ri-logout-box-r-line'),
                ]),
            ])
            ->defaultSort('last_activity', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('logout_all')
                ->label('Logout All Sessions')
                ->icon('ri-logout-box-r-line')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Logout All Sessions')
                ->modalDescription("Are you sure you want to logout all sessions for {$this->getOwnerRecord()->email}? This will force the user to login again.")
                ->action(function () {
                    $this->getOwnerRecord()->sessions()->delete();
                })
                ->successNotification(
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('All sessions logged out successfully')
                ),
        ];
    }
}
