<?php

namespace App\Admin\Resources;

use App\Admin\Resources\FailedJobResource\Pages;
use App\Models\FailedJob;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static ?string $navigationIcon = 'ri-error-warning-line';

    protected static ?string $activeNavigationIcon = 'ri-error-warning-fill';

    protected static ?string $navigationGroup = 'Other';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payload')->formatStateUsing(function ($state) {
                    $state = json_decode($state);

                    // List displayName
                    return $state->displayName;
                }),
                Tables\Columns\TextColumn::make('exception')->formatStateUsing(function ($state) {
                    return explode("\n", $state)[0];
                })->wrap()->tooltip(function ($state) {
                    return $state;
                })->limit(200),
                Tables\Columns\TextColumn::make('failed_at'),
            ])
            ->poll()
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->label('Retry')
                    ->requiresConfirmation()
                    ->action(function (FailedJob $record): void {
                        Artisan::call("queue:retry {$record->uuid}");
                        Notification::make()
                            ->title("The job with uuid '{$record->uuid}' has been pushed back onto the queue.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('delete')
                    ->label('Mark as Resolved')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (FailedJob $failedJob) {
                        $failedJob->delete();
                    }),
            ])
            ->bulkActions([
                BulkAction::make('retry')
                    ->label('Retry')
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            try {
                                Artisan::call("queue:retry {$record->uuid}");
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        }
                        Notification::make()
                            ->title("{$records->count()} jobs have been pushed back onto the queue.")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('failed_at', 'desc')
            ->filters([

            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailedJobs::route('/'),
        ];
    }
}
