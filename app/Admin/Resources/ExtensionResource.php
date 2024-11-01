<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ExtensionResource\Pages;
use App\Models\Extension;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExtensionResource extends Resource
{
    protected static ?string $model = Extension::class;

    protected static ?string $navigationGroup = 'Extensions';

    protected static ?string $navigationIcon = 'ri-puzzle-line';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    public static function form(Form $form): Form
    {
        $extensions = \App\Helpers\ExtensionHelper::getAvailableExtensions();
        $options = \App\Helpers\ExtensionHelper::convertToOptions($extensions);

        return $form
            ->schema([
                Forms\Components\Checkbox::make('enabled'),
                Section::make('Extension Settings')
                    ->description('Specific settings for the selected extension')
                    ->schema([
                        Grid::make()->schema(fn (Get $get): array => $options->settings[$get('extension')] ?? $options->settings['default'])->key('settings'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // Read whole base_path('extensions') directory and create a new Extension model for each extension (if it doesn't exist)
        foreach (scandir(base_path('extensions')) as $extension) {
            if (in_array($extension, ['.', '..', 'Gateways', 'Servers'])) {
                continue;
            }

            $type = strtolower($extension);
            // Remove the 's' from  end of the type
            $type = substr($type, 0, -1);

            foreach (scandir(base_path('extensions/' . $extension)) as $extension) {
                if (in_array($extension, ['.', '..'])) {
                    continue;
                }

                Extension::firstOrCreate([
                    'extension' => $extension,
                    'type' => $type,
                    'name' => $extension,
                ]);
            }
        }

        return parent::getEloquentQuery()->whereNotIn('type', ['gateway', 'server']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtensions::route('/'),
            'edit' => Pages\EditExtension::route('/{record}/edit'),
        ];
    }
}
