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

    public static function getNavigationLabel(): string
    {
        return __('emails.email_logs');
    }

    public static function getModelLabel(): string
    {
        return __('emails.email_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('emails.email_logs');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subject')
                    ->size(TextSize::Medium)
                    ->label(__('emails.subject')),
                TextEntry::make('to')
                    ->size(TextSize::Medium)
                    ->label(__('emails.to')),
                TextEntry::make('sent_at')
                    ->size(TextSize::Medium)
                    ->date()
                    ->hidden(fn ($state) => $state === null)
                    ->label(__('emails.sent_at')),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (EmailLog $record) => match ($record->status) {
                        'pending' => 'gray',
                        'sent' => 'success',
                        'failed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => __('emails.' . $state))
                    ->label(__('emails.status')),

                TextEntry::make('error')
                    ->columnSpanFull()
                    ->size(TextSize::Medium)
                    ->hidden(fn ($state) => $state === null)
                    ->label(__('emails.error')),
                View::make('admin.infolists.components.iframe')->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label(__('emails.subject'))
                    ->searchable(),
                TextColumn::make('to')
                    ->label(__('emails.to'))
                    ->searchable(),
                TextColumn::make('sent_at')
                    ->label(__('emails.sent_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('emails.status'))
                    ->badge()
                    ->color(fn (EmailLog $record) => match ($record->status) {
                        'pending' => 'gray',
                        'sent' => 'success',
                        'failed' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => __('emails.' . $state))
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
