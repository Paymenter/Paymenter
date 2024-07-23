<?php

namespace App\Admin\Resources;

use App\Admin\Resources\FailedJobResource\Pages;
use App\Models\FailedJob;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid'),
                Tables\Columns\TextColumn::make('exception')->formatStateUsing(function ($state) {
                    return explode("\n", $state)[0];
                })->wrap(),
                Tables\Columns\TextColumn::make('failed_at'),
            ])
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->label('Retry')
                    ->requiresConfirmation()
                    ->action(function (FailedJob $failedJob) {
                        $result = $failedJob->retry();
                        if ($result === null) {
                            Notification::make()
                                ->success()
                                ->title('Job Retried')
                                ->body('The job has been retried successfully.')
                                ->send();
                        } else {
                            Notification::make()
                                ->danger()
                                ->title('Job Retry Failed')
                                ->body('The job could not be retried: ' . $result)
                                ->send();
                        }
                    }),
            ])
            ->filters([
                //
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
