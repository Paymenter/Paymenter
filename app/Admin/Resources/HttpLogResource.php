<?php

namespace App\Admin\Resources;

use App\Admin\Resources\HttpLogResource\Pages\ListHttpLogs;
use App\Models\DebugLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HttpLogResource extends Resource
{
    protected static ?string $model = DebugLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-external-link-line';

    protected static ?string $modelLabel = 'HTTP log';

    public static string|\UnitEnum|null $navigationGroup = 'Debug';

    // Edit query to only include with type 'http'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'http');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('method')
                    ->state(function (DebugLog $record) {
                        return $record->context['method'] ?? null;
                    })
                    ->toggleable(),
                TextColumn::make('url')
                    ->state(function (DebugLog $record) {
                        return $record->context['url'] ?? null;
                    })
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->where('context->url', 'like', "%{$search}%"))
                    ->toggleable(),
                TextColumn::make('status')
                    ->state(function (DebugLog $record) {
                        return $record->context['response_status'] ?? null;
                    })
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->where('context->response_status', 'like', "%{$search}%"))
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            ->components([
                TextEntry::make('method')
                    ->state(function (DebugLog $record) {
                        return $record->context['method'] ?? null;
                    }),
                TextEntry::make('url')
                    ->state(function (DebugLog $record) {
                        return $record->context['url'] ?? null;
                    }),

                TextEntry::make('status')
                    ->state(function (DebugLog $record) {
                        return $record->context['response_status'] ?? null;
                    }),

                ViewEntry::make('request')
                    ->label('Request')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['payload'] ?? null;
                    })->columnSpanFull(),

                ViewEntry::make('request_headers')
                    ->label('Request Headers')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['headers'] ?? null;
                    })->columnSpanFull(),

                ViewEntry::make('response_headers')
                    ->label('Response Headers')
                    ->view('admin.infolists.components.json')
                    ->state(function (DebugLog $record) {
                        return $record->context['response_headers'] ?? null;
                    })->columnSpanFull(),

                ViewEntry::make('response')
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
            'index' => ListHttpLogs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasPermission('admin.debug_logs.view');
    }
}
