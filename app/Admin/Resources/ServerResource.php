<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ServerResource\Pages\CreateServer;
use App\Admin\Resources\ServerResource\Pages\EditServer;
use App\Admin\Resources\ServerResource\Pages\ListServers;
use App\Helpers\ExtensionHelper;
use App\Models\Server;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    public static function getNavigationLabel(): string
    {
        return __('servers.servers');
    }

    public static function getModelLabel(): string
    {
        return __('servers.server_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('servers.servers_plural_label');
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-server-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-server-fill';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    public static function form(Schema $schema): Schema
    {
        $servers = ExtensionHelper::getExtensions('server');

        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('servers.name'))
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        static::getModel(),
                        'name',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule) => $rule->where('deleted_at', null)
                    )
                    ->placeholder(__('servers.enter_name')),
                Select::make('extension')
                    ->label(__('servers.server'))
                    ->required()
                    ->searchable()
                    ->options(array_combine(
                        array_column($servers, 'name'),
                        array_column($servers, 'name')
                    ))
                    ->live(onBlur: true)
                    ->disabledOn('edit')
                    ->afterStateUpdated(fn (Select $component) => $component
                        ->getContainer()
                        ->getComponent('settings')
                        ->getChildSchema()
                        ->fill())
                    ->placeholder(__('servers.select_server'))
                    ->hintAction(
                        Action::make('Test Configuration')
                            ->action(function (Get $get, $record) {
                                // Dd settings
                                $connection = ExtensionHelper::testConfig($record, $get('settings'));

                                if ($connection === true) {
                                    Notification::make()
                                        ->title(__('servers.config_correct'))
                                        ->success()->send();
                                } else {
                                    Notification::make()
                                        ->title(__('servers.connection_failed') . $connection)
                                        ->danger()->send();
                                }
                            })
                            ->label(__('servers.test_connection'))
                            ->hidden(function ($record) {
                                // If record is empty or textConfig is not available, then hide the button
                                return empty($record) || !ExtensionHelper::hasFunction($record, 'testConfig');
                            })
                    ),
                Section::make(__('servers.settings_heading'))
                    ->columnSpanFull()
                    ->description(__('servers.settings_description'))
                    ->schema([
                        Grid::make()->schema(fn (Get $get) => ExtensionHelper::getConfigAsInputs('server', $get('extension'), $get('settings')))->key('settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('servers.name'))
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
            'index' => ListServers::route('/'),
            'create' => CreateServer::route('/create'),
            'edit' => EditServer::route('/{record}/edit'),
        ];
    }
}
