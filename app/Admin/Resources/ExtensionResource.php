<?php

namespace App\Admin\Resources;

use App\Admin\Clusters\Extensions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use App\Admin\Resources\ExtensionResource\Pages\ListExtensions;
use App\Admin\Resources\ExtensionResource\Pages\EditExtension;
use App\Admin\Resources\ExtensionResource\Pages;
use App\Admin\Resources\ExtensionResource\Pages\ListAvailableExtensions;
use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static string | \BackedEnum | null $navigationIcon = 'ri-puzzle-line';

    protected static string | \BackedEnum | null $activeNavigationIcon = 'ri-puzzle-fill';

    protected static ?string $cluster = Extensions::class;

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
        return $schema
            ->components([
                Toggle::make('enabled'),
                Section::make('Extension Settings')
                    ->columnSpanFull()
                    ->description('Specific settings for the selected extension')
                    ->schema([
                        Grid::make()->schema(fn (Get $get) => ExtensionHelper::getConfigAsInputs('other', $get('extension'), $get('settings')))->key('settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // // Read whole base_path('extensions') directory and create a new Extension model for each extension (if it doesn't exist)
        // foreach (scandir(base_path('extensions')) as $extension) {
        //     if (in_array($extension, ['.', '..', 'Gateways', 'Servers'])) {
        //         continue;
        //     }

        //     $type = strtolower($extension);
        //     // Remove the 's' from  end of the type
        //     $type = substr($type, 0, -1);

        //     foreach (scandir(base_path('extensions/' . $extension)) as $extension) {
        //         if (in_array($extension, ['.', '..'])) {
        //             continue;
        //         }

        //         Extension::firstOrCreate([
        //             'extension' => $extension,
        //             'type' => $type,
        //             'name' => $extension,
        //         ]);
        //     }
        // }

        return parent::getEloquentQuery()->whereNotIn('type', ['gateway', 'server']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExtensions::route('/'),
            'edit' => EditExtension::route('/{record}/edit'),
        ];
    }
}
