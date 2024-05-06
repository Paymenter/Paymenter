<?php

namespace App\Admin\Resources;

use App\Admin\Resources\GatewayResource\Pages;
use App\Admin\Resources\GatewayResource\RelationManagers;
use App\Models\Gateway;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Components\Select;
use App\Classes\FilamentInput;
use Filament\Forms\Components\Section;

class GatewayResource extends Resource
{
    protected static ?string $model = Gateway::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        $gateways = \App\Helpers\ExtensionHelper::getAvailableGateways();
        $options = [];
        $gatewaySettings = ['default' => []];
        foreach ($gateways as $gateway) {
            $options[$gateway['name']] = $gateway['name'];
            foreach ($gateway['settings'] as $setting) {
                $gatewaySettings[$gateway['name']][] = FilamentInput::convert($setting);
            }
        }

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(static::getModel(), 'name', ignoreRecord: true)
                    ->placeholder('Enter the name of the gateway'),
                Forms\Components\Select::make('extension')
                    ->label('Gateway')
                    ->required()
                    ->searchable()
                    ->options($options)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Select $component) => $component
                        ->getContainer()
                        ->getComponent('settings')
                        ->getChildComponentContainer()
                        ->fill())
                    ->placeholder('Select the type of the gateway'),
                Section::make('Gateway Settings')
                    ->description('Specific settings for the selected gateway')
                    ->schema([
                        Grid::make()->schema(fn (Get $get): array => $gatewaySettings[$get('extension')] ?? $gatewaySettings['default'])->key('settings')
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
            'index' => Pages\ListGateways::route('/'),
            'create' => Pages\CreateGateway::route('/create'),
            'edit' => Pages\EditGateway::route('/{record}/edit'),
        ];
    }
}
