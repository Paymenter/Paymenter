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
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;

class OauthClientResource extends Resource
{
    protected static ?string $model = OauthClient::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-lock-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-lock-2-fill';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function getNavigationLabel(): string
    {
        return __('oauth.oauth_clients');
    }

    public static function getModelLabel(): string
    {
        return __('oauth.oauth_client');
    }

    public static function getPluralModelLabel(): string
    {
        return __('oauth.oauth_clients');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('oauth.name'))
                    ->required()
                    ->maxLength(255),
                TagsInput::make('redirect')
                    ->label(__('oauth.redirect_uris'))
                    ->required()
                    ->separator(',')
                    ->columnSpanFull(),
                TextInput::make('secret')
                    ->label(__('oauth.client_secret'))
                    ->disabled()
                    ->formatStateUsing(fn ($record) => '********')
                    ->hiddenOn(['create'])
                    ->suffixAction(
                        Action::make('regenerate')
                            ->label(__('oauth.regenerate'))
                            ->icon('heroicon-s-arrow-path')
                            ->requiresConfirmation()
                            ->modalDescription(__('oauth.regenerate_desc'))
                            ->action(function ($livewire, OauthClient $record) {
                                $clientRepository = new ClientRepository;
                                $clientRepository->regenerateSecret($record);

                                $livewire->js(
                                    'window.navigator.clipboard.writeText(' . Js::from($record->plainSecret) . ');'
                                );

                                Notification::make()
                                    ->title(__('oauth.secret_regenerated'))
                                    ->body(Str::markdown(__('oauth.secret_regenerated_body', ['secret' => $record->plainSecret])))
                                    ->icon('heroicon-o-lock-closed')
                                    ->persistent()
                                    ->success()
                                    ->send();
                            })
                    ),
                TextInput::make('client_id')
                    ->label(__('oauth.client_id'))
                    ->disabled()
                    ->formatStateUsing(fn ($record) => $record?->id)
                    ->hiddenOn(['create'])
                    ->suffixAction(
                        Action::make('copy')
                            ->icon('heroicon-s-clipboard-document-check')
                            ->action(function ($livewire, $state) {
                                $livewire->js(
                                    'window.navigator.clipboard.writeText(' . Js::from($state) . ');
                                        $tooltip("' . __('oauth.copied_to_clipboard') . '", { timeout: 1500 });'
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
                    ->label(__('oauth.name'))
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
