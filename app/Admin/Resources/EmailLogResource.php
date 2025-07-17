<?php

namespace App\Admin\Resources;

use App\Admin\Resources\EmailLogResource\Pages\ListEmailLogs;
use App\Admin\Resources\EmailLogResource\Pages\ViewEmailLog;
use App\Models\EmailLog;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-mail-send-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-mail-send-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject')
                    ->size(TextSize::Medium)
                    ->label('Subject'),
                TextEntry::make('to')
                    ->size(TextSize::Medium)
                    ->label('To'),
                TextEntry::make('sent_at')
                    ->size(TextSize::Medium)
                    ->date()
                    ->hidden(fn ($state) => $state === null)
                    ->label('Sent At'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (EmailLog $record) => match ($record->status) {
                        'pending' => 'gray',
                        'sent' => 'success',
                        'failed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->label('Status'),

                TextEntry::make('error')
                    ->columnSpanFull()
                    ->size(TextSize::Medium)
                    ->hidden(fn ($state) => $state === null)
                    ->label('Error'),
                View::make('admin.infolists.components.iframe')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('to')
                    ->searchable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (EmailLog $record) => match ($record->status) {
                        'pending' => 'gray',
                        'sent' => 'success',
                        'failed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->searchable(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('id', 'desc');
            })
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailLogs::route('/'),
            'view' => ViewEmailLog::route('/{record}'),
        ];
    }
}
