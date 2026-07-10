<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ConfigOptionResource\Pages\CreateConfigOption;
use App\Admin\Resources\ConfigOptionResource\Pages\EditConfigOption;
use App\Admin\Resources\ConfigOptionResource\Pages\ListConfigOptions;
use App\Models\ConfigOption;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConfigOptionResource extends Resource
{
    protected static ?string $model = ConfigOption::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-equalizer-2-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-equalizer-2-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Tab::make('General')->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Enter the name of the configuration option'),
                            TextInput::make('description')
                                ->label('Description')
                                ->columnSpanFull()
                                ->maxLength(255)
                                ->placeholder('Enter the description of the configuration option'),
                            TextInput::make('env_variable')
                                ->label('Environment Variable')
                                ->maxLength(255)
                                ->placeholder('Enter the environment variable name'),
                            Select::make('type')
                                ->label('Type')
                                ->native(false)
                                ->required()
                                ->reactive()
                                ->options([
                                    'text' => 'Text',
                                    'number' => 'Number',
                                    'select' => 'Select',
                                    'radio' => 'Radio',
                                    'checkbox' => 'Checkbox',
                                    'slider' => 'Slider',
                                ]),
                            Checkbox::make('hidden')
                                ->label('Hidden'),
                            Checkbox::make('upgradable')
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider']))
                                ->label('Upgradable')
                                ->helperText('If enabled, this configuration option can be upgraded in the future.'),
                            Select::make('products')
                                ->label('Products')
                                ->relationship('products', 'name')
                                ->multiple()
                                ->preload()
                                ->live()
                                ->placeholder('Select the products that this configuration option belongs to'),
                        ]),
                        Tab::make('Options')
                            ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio', 'slider', 'checkbox']))
                            ->schema([
                                Repeater::make('Options')
                                    ->relationship('children')
                                    ->label('Options')
                                    ->addActionLabel('Add Option')
                                    ->columnSpanFull()
                                    ->itemLabel(fn (array $state) => $state['name'])
                                    ->collapsible()
                                    ->collapsed()
                                    ->cloneable()
                                    ->reorderable()
                                    ->orderColumn('sort')
                                    ->columns(2)
                                    // When the type is checkbox only allow 1 child
                                    ->maxItems(function (Get $get): ?int {
                                        if (in_array($get('type'), ['select', 'radio', 'slider'])) {
                                            return null; // unlimited children
                                        }

                                        return 1; // checkbox
                                    })
                                    ->minItems(1)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->live()
                                            ->maxLength(255)
                                            ->placeholder('Enter the name of the configuration option'),
                                        TextInput::make('env_variable')
                                            ->label('Environment Variable')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Enter the environment variable name'),
                                        Select::make('products')
                                            ->label('Products')
                                            ->relationship('products', 'name', fn (Builder $query, Get $get) => $query->whereIn('products.id', $get('../../products') ?? []))
                                            ->multiple()
                                            ->preload()
                                            ->placeholder('Select the products that this option belongs to')
                                            ->default(function (Get $get) {
                                                return $get('../../products') ?? [];
                                            })
                                            ->columnSpanFull(),
                                        // if the type is select, radio or checkbox then allow unlimited children (otherwise only allow 1)
                                        ProductResource::plan()->columnSpanFull()->label('Pricing')->reorderable(false)->deleteAction(null),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('env_variable')
                    ->label('Environment Variable')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hidden')
                    ->badge()
                    ->label('Hidden')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('parent_id', null))
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('clone')
                    ->label('Clone')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('info')
                    ->action(function (ConfigOption $record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            // 1. Replicate parent
                            $clone = $record->replicate();
                            $clone->name = $clone->name . ' (Clone)';
                            $clone->save();

                            // 2. Replicate children
                            foreach ($record->children as $child) {
                                $childClone = $child->replicate();
                                $childClone->parent_id = $clone->id;
                                $childClone->save();

                                // 3. Replicate plans under the child
                                foreach ($child->plans as $plan) {
                                    $planClone = $plan->replicate();
                                    $planClone->priceable_id = $childClone->id;
                                    $planClone->priceable_type = get_class($childClone);
                                    $planClone->save();

                                    // 4. Replicate prices under the plan
                                    foreach ($plan->prices as $price) {
                                        $priceClone = $price->replicate();
                                        $priceClone->plan_id = $planClone->id;
                                        $priceClone->save();
                                    }
                                }

                                // 5. Replicate product mapping for child options
                                $childClone->products()->sync($child->products->pluck('id'));
                            }

                            // 6. Replicate product mapping for parent option
                            $clone->products()->sync($record->products->pluck('id'));
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Config Option Cloned Successfully')
                            ->success()
                            ->send();
                    })
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('sort', 'asc');
            })
            ->reorderable('sort');
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
            'index' => ListConfigOptions::route('/'),
            'create' => CreateConfigOption::route('/create'),
            'edit' => EditConfigOption::route('/{record}/edit'),
        ];
    }
}
