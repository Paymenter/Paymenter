<?php

namespace App\Admin\Resources;

use App\Admin\Resources\OauthClientResource\Pages\CreateOauthClient;
use App\Admin\Resources\OauthClientResource\Pages\EditOauthClient;
use App\Admin\Resources\OauthClientResource\Pages\ListOauthClients;
use App\Models\OauthClient;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OauthClientResource extends Resource
{
    protected static ?string $model = OauthClient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-lock-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-lock-2-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TagsInput::make('redirect')
                    ->required()
                    ->separator(',')
                    ->columnSpanFull(),
                TextInput::make('secret')
                    ->disabled()
                    ->formatStateUsing(fn ($record) => $record?->secret)
                    ->hiddenOn(['create'])
                    ->suffixAction(
                        Action::make('copy')
                            ->icon('heroicon-s-clipboard-document-check')
                            ->action(function ($livewire, $state) {
                                $livewire->js(
                                    'window.navigator.clipboard.writeText("' . $state . '");
                                        $tooltip("' . __('Copied to clipboard') . '", { timeout: 1500 });'
                                );
                            })
                    ),
                TextInput::make('client_id')
                    ->disabled()
                    ->formatStateUsing(fn ($record) => $record?->id)
                    ->hiddenOn(['create'])
                    ->suffixAction(
                        Action::make('copy')
                            ->icon('heroicon-s-clipboard-document-check')
                            ->action(function ($livewire, $state) {
                                $livewire->js(
                                    'window.navigator.clipboard.writeText("' . $state . '");
                                        $tooltip("' . __('Copied to clipboard') . '", { timeout: 1500 });'
                                );
                            })
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => ListOauthClients::route('/'),
            'create' => CreateOauthClient::route('/create'),
            'edit' => EditOauthClient::route('/{record}/edit'),
        ];
    }
}
