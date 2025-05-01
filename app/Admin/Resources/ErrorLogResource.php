<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ErrorLogResource\Pages;
use App\Models\DebugLog;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ErrorLogResource extends Resource
{
    protected static ?string $model = DebugLog::class;

    protected static ?string $navigationIcon = 'ri-bug-line';

    protected static ?string $modelLabel = 'Error log';

    public static ?string $navigationGroup = 'Debug';

    // Edit query to only include with type 'http'
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('type', 'exception');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('message')
                    ->state(function (DebugLog $record) {
                        return $record->context['message'] ?? null;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('file')
                    ->state(function (DebugLog $record) {
                        return $record->context['file'] ?? null;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('line')
                    ->state(function (DebugLog $record) {
                        return $record->context['line'] ?? null;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('settings.debug', false);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                Infolists\Components\TextEntry::make('message')
                    ->state(function (DebugLog $record) {
                        return $record->context['message'] ?? null;
                    }),
                Infolists\Components\TextEntry::make('file')
                    ->state(function (DebugLog $record) {
                        return $record->context['file'] ?? null;
                    }),
                Infolists\Components\TextEntry::make('line')
                    ->state(function (DebugLog $record) {
                        return $record->context['line'] ?? null;
                    }),

                Infolists\Components\TextEntry::make('trace')
                    ->label('Trace')
                    ->state(function (DebugLog $record) {
                        return $record->context['trace'] ?? null;
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErrorLogs::route('/'),
        ];
    }
}
