<?php

namespace App\Admin\Resources;

use App\Admin\Resources\HttpLogResource\Pages;
use App\Models\DebugLog;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HttpLogResource extends Resource
{
    protected static ?string $model = DebugLog::class;

    protected static ?string $navigationIcon = 'ri-external-link-line';

    protected static ?string $modelLabel = 'HTTP log';

    public static ?string $navigationGroup = 'Debug';

    // Edit query to only include with type 'http'
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('type', 'http');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('method')
                    ->state(function (DebugLog $record) {
                        return $record->context['method'] ?? null;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('url')
                    ->state(function (DebugLog $record) {
                        return $record->context['url'] ?? null;
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->state(function (DebugLog $record) {
                        return $record->context['response_status'] ?? null;
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
            ->schema([
                Infolists\Components\TextEntry::make('method')
                    ->state(function (DebugLog $record) {
                        return $record->context['method'] ?? null;
                    }),
                Infolists\Components\TextEntry::make('url')
                    ->state(function (DebugLog $record) {
                        return $record->context['url'] ?? null;
                    }),

                Infolists\Components\TextEntry::make('status')
                    ->state(function (DebugLog $record) {
                        return $record->context['response_status'] ?? null;
                    }),

                Infolists\Components\ViewEntry::make('request')
                    ->label('Request')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['payload'] ?? null;
                    })->columnSpanFull(),

                Infolists\Components\ViewEntry::make('request_headers')
                    ->label('Request Headers')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['headers'] ?? null;
                    })->columnSpanFull(),

                Infolists\Components\ViewEntry::make('response_headers')
                    ->label('Response Headers')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['response_headers'] ?? null;
                    })->columnSpanFull(),

                Infolists\Components\ViewEntry::make('response')
                    ->label('Response')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['response'] ?? null;
                    })->columnSpanFull(),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHttpLogs::route('/'),
        ];
    }
}
