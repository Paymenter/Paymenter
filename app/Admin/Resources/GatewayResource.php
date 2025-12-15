<?php

namespace App\Admin\Resources;

use App\Admin\Resources\GatewayResource\Pages\CreateGateway;
use App\Admin\Resources\GatewayResource\Pages\EditGateway;
use App\Admin\Resources\GatewayResource\Pages\ListGateways;
use App\Helpers\ExtensionHelper;
use App\Models\Gateway;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class GatewayResource extends Resource
{
    protected static ?string $model = Gateway::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Extensions';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-secure-payment-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-secure-payment-fill';

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
        $gateways = ExtensionHelper::getExtensions('gateway');

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(static::getModel(), 'name', ignoreRecord: true)
                    ->placeholder('Enter the name of the gateway'),
                Select::make('extension')
                    ->label('Gateway')
                    ->required()
                    ->searchable()
                    ->unique(
                        static::getModel(),
                        'extension',
                        ignoreRecord: true,
                        modifyRuleUsing: fn ($rule) => $rule->where('deleted_at', null)
                    )
                    ->options(array_combine(
                        array_column($gateways, 'name'),
                        array_column($gateways, 'name')
                    ))
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Select $component) => $component
                        ->getContainer()
                        ->getComponent('settings')
                        ->getChildComponentContainer()
                        ->fill())
                    ->placeholder('Select the type of the gateway'),
                Section::make('Gateway Settings')
                    ->columnSpanFull()
                    ->description('Specific settings for the selected gateway')
                    ->schema([
                        Grid::make()->schema(fn (Get $get) => ExtensionHelper::getConfigAsInputs('gateway', $get('extension'), $get('settings')))->key('settings'),
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
            'index' => ListGateways::route('/'),
            'create' => CreateGateway::route('/create'),
            'edit' => EditGateway::route('/{record}/edit'),
        ];
    }
}
