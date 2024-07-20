<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ServerResource\Pages;
use App\Helpers\ExtensionHelper;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    protected static ?string $navigationGroup = 'Extensions';
    
    protected static ?string $navigationIcon = 'heroicon-o-server';

    public static function form(Form $form): Form
    {
        $servers = \App\Helpers\ExtensionHelper::getAvailableServers();
        $options = \App\Helpers\ExtensionHelper::convertToOptions($servers);

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(static::getModel(), 'name', ignoreRecord: true)
                    ->placeholder('Enter the name of the server'),
                Forms\Components\Select::make('extension')
                    ->label('Server')
                    ->required()
                    ->searchable()
                    ->options($options->options)
                    ->live(onBlur: true)
                    ->disabledOn('edit')
                    ->afterStateUpdated(fn (Select $component) => $component
                        ->getContainer()
                        ->getComponent('settings')
                        ->getChildComponentContainer()
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
                    ->description('Specific settings for the selected server')
                    ->schema([
                        Grid::make()->schema(fn (Get $get): array => $options->settings[$get('extension')] ?? $options->settings['default'])->key('settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListServers::route('/'),
            'create' => Pages\CreateServer::route('/create'),
            'edit' => Pages\EditServer::route('/{record}/edit'),
        ];
    }
}
