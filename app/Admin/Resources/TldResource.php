<?php

namespace App\Admin\Resources;

use App\Admin\Resources\TldResource\Pages\CreateTld;
use App\Admin\Resources\TldResource\Pages\EditTld;
use App\Admin\Resources\TldResource\Pages\ListTlds;
use App\Admin\Resources\TldResource\RelationManagers\PricesRelationManager;
use App\Helpers\ExtensionHelper;
use App\Models\Product;
use App\Models\Tld;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TldResource extends Resource
{
    protected static ?string $model = Tld::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-global-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-global-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tld')
                    ->label('TLD')
                    ->required()
                    ->maxLength(63)
                    ->placeholder('com'),
                Select::make('server_id')
                    ->label('Registrar')
                    ->relationship('server', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('product_id')
                    ->label('Domain product')
                    ->options(fn () => Product::query()
                        ->with('server')
                        ->orderBy('name')
                        ->get()
                        ->filter(fn (Product $product) => $product->server && ExtensionHelper::isRegistrar($product->server))
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->helperText('Product used by the domain search page for this TLD.'),
                Toggle::make('enabled')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tld')->searchable()->sortable(),
                TextColumn::make('server.name')->label('Registrar')->placeholder('Global'),
                IconColumn::make('enabled')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->authorize('update'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->authorize('deleteAny'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTlds::route('/'),
            'create' => CreateTld::route('/create'),
            'edit' => EditTld::route('/{record}/edit'),
        ];
    }
}
