<?php

namespace App\Admin\Resources;

use App\Admin\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use Filament\Infolists;
use Filament\Infolists\Components;
use Filament\Infolists\Components\View;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static ?string $navigationIcon = 'ri-mail-send-line';

    protected static ?string $activeNavigationIcon = 'ri-mail-send-fill';

    protected static ?string $navigationGroup = 'Other';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('subject')
                    ->size(Components\TextEntry\TextEntrySize::Medium)
                    ->label('Subject'),
                Infolists\Components\TextEntry::make('to')
                    ->size(Components\TextEntry\TextEntrySize::Medium)
                    ->label('To'),
                Infolists\Components\TextEntry::make('sent_at')
                    ->size(Components\TextEntry\TextEntrySize::Medium)
                    ->date()
                    ->hidden(fn ($state) => $state === null)
                    ->label('Sent At'),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn (EmailLog $record) => match ($record->status) {
                        'pending' => 'gray',
                        'sent' => 'success',
                        'failed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->label('Status'),

                Infolists\Components\TextEntry::make('error')
                    ->columnSpanFull()
                    ->size(Components\TextEntry\TextEntrySize::Medium)
                    ->hidden(fn ($state) => $state === null)
                    ->label('Error'),
                View::make('admin.infolists.components.iframe')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }
}
