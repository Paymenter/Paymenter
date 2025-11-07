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
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        static::getModel(),
                        'name',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule) => $rule->where('deleted_at', null)
                    )
                    ->placeholder('Enter the name of the server'),
                Select::make('extension')
                    ->label('Server')
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
                    ->placeholder('Select the type of the server')
                    ->hintAction(
                        Action::make('Test Configuration')
                            ->action(function (Get $get, $record) {
                                // Dd settings
                                $connection = ExtensionHelper::testConfig($record, $get('settings'));

                                if ($connection === true) {
                                    Notification::make()
                                        ->title('Configuration is correct')
                                        ->success()->send();
                                } else {
                                    Notification::make()
                                        ->title('Connection failed: ' . $connection)
                                        ->danger()->send();
                                }
                            })
                            ->label('Test Connection')
                            ->hidden(function ($record) {
                                // If record is empty or textConfig is not available, then hide the button
                                return empty($record) || !ExtensionHelper::hasFunction($record, 'testConfig');
                            })
                    ),
                Section::make('Server Settings')
                    ->columnSpanFull()
                    ->description('Specific settings for the selected server')
                    ->schema([
                        Grid::make()->schema(fn (Get $get) => ExtensionHelper::getConfigAsInputs('server', $get('extension'), $get('settings')))->key('settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
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
