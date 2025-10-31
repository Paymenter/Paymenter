<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ErrorLogResource\Pages\ListErrorLogs;
use App\Models\DebugLog;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ErrorLogResource extends Resource
{
    protected static ?string $model = DebugLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-bug-line';

    protected static ?string $modelLabel = 'Error log';

    public static string|\UnitEnum|null $navigationGroup = 'Debug';

    // Edit query to only include with type 'http'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'exception');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('message')
                    ->state(function (DebugLog $record) {
                        return $record->context['message'] ?? null;
                    })
                    ->toggleable(),
                TextColumn::make('file')
                    ->state(function (DebugLog $record) {
                        return $record->context['file'] ?? null;
                    })
                    ->toggleable(),
                TextColumn::make('line')
                    ->state(function (DebugLog $record) {
                        return $record->context['line'] ?? null;
                    })
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('settings.debug', false);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('message')
                    ->state(function (DebugLog $record) {
                        return $record->context['message'] ?? null;
                    }),
                TextEntry::make('file')
                    ->state(function (DebugLog $record) {
                        return $record->context['file'] ?? null;
                    }),
                TextEntry::make('line')
                    ->state(function (DebugLog $record) {
                        return $record->context['line'] ?? null;
                    }),

                TextEntry::make('trace')
                    ->label('Trace')
                    ->state(function (DebugLog $record) {
                        return $record->context['trace'] ?? null;
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErrorLogs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermission('admin.debug_logs.view');
    }
}
