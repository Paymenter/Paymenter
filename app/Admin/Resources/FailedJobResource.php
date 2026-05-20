<?php

namespace App\Admin\Resources;

use App\Admin\Resources\FailedJobResource\Pages\ListFailedJobs;
use App\Models\FailedJob;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-error-warning-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-error-warning-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function getNavigationLabel(): string
    {
        return __('jobs.failed_jobs');
    }

    public static function getModelLabel(): string
    {
        return __('jobs.failed_job');
    }

    public static function getPluralModelLabel(): string
    {
        return __('jobs.failed_jobs');
    }

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
                TextColumn::make('uuid')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payload')
                    ->label(__('jobs.job_name'))
                    ->formatStateUsing(function ($state) {
                        $state = json_decode($state);

                        // List displayName
                        return $state->displayName;
                    }),
                TextColumn::make('exception')
                    ->label(__('jobs.exception'))
                    ->formatStateUsing(function ($state) {
                        return explode("\n", $state)[0];
                    })->wrap()->tooltip(function ($state) {
                        return $state;
                    })->limit(200),
                TextColumn::make('failed_at')
                    ->label(__('jobs.failed_at')),
            ])
            ->poll()
            ->recordActions([
                Action::make('retry')
                    ->label(__('jobs.retry'))
                    ->requiresConfirmation()
                    ->action(function (FailedJob $record): void {
                        Artisan::call("queue:retry {$record->uuid}");
                        Notification::make()
                            ->title(__('jobs.retry_success_single', ['uuid' => $record->uuid]))
                            ->success()
                            ->send();
                    }),
                Action::make('delete')
                    ->label(__('jobs.resolve'))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (FailedJob $failedJob) {
                        $failedJob->delete();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('retry')
                    ->label(__('jobs.retry'))
                    ->requiresConfirmation()
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            try {
                                Artisan::call("queue:retry {$record->uuid}");
                            } catch (Exception $e) {
                                report($e);
                                Notification::make()
                                    ->title($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        }
                        Notification::make()
                            ->title(__('jobs.retry_success_multiple', ['count' => $records->count()]))
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('delete')
                    ->label(__('jobs.resolve'))
                    ->requiresConfirmation()
                    ->color('danger')
                    ->action(function (Collection $records): void {
                        foreach ($records as $record) {
                            $record->delete();
                        }
                        Notification::make()
                            ->title(__('jobs.resolve_success_multiple', ['count' => $records->count()]))
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('failed_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedJobs::route('/'),
        ];
    }
}
