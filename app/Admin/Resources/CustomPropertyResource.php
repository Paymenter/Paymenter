<?php

namespace App\Admin\Resources;

use App\Admin\Resources\CustomPropertyResource\Pages\CreateCustomProperty;
use App\Admin\Resources\CustomPropertyResource\Pages\EditCustomProperty;
use App\Admin\Resources\CustomPropertyResource\Pages\ListCustomProperty;
use App\Models\CustomProperty;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Actions\Action as TableAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class CustomPropertyResource extends Resource
{
    protected static ?array $conditionPropertyOptionsCache = null;

    protected static array $conditionValueOptionsCache = [];

    protected static ?string $model = CustomProperty::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-list-settings-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-list-settings-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('key')->required(),
                Select::make('model')->options([
                    'App\Models\User' => 'User',
                ])->required()->reactive(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Leave empty to append automatically.')
                    ->helperText('Controls the display order in forms.')
                    ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (int) $state : null),
                Select::make('type')->options([
                    'string' => 'Short Text',
                    'text' => 'Long Text',
                    'number' => 'Number',
                    'select' => 'Select',
                    'checkbox' => 'Checkbox',
                    'radio' => 'Radio',
                    'date' => 'Date',
                ])->required(),
                Textarea::make('description')->nullable()->columnSpanFull()->rows(2),
                TagsInput::make('allowed_values')
                    ->label('Allowed values')
                    ->helperText('Required when the type is Select or Radio.')
                    ->placeholder('Add values and press enter')
                    ->default([])
                    ->reactive()
                    ->required(fn (Get $get) => in_array($get('type'), ['select', 'radio'], true))
                    ->rules(fn (Get $get) => in_array($get('type'), ['select', 'radio'], true) ? ['required', 'array', 'min:1'] : ['nullable', 'array'])
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_array($state)) {
                            return [];
                        }

                        return collect($state)
                            ->map(function ($value) {
                                if (! is_scalar($value)) {
                                    return null;
                                }

                                $trimmed = trim((string) $value);

                                return $trimmed === '' ? null : $trimmed;
                            })
                            ->filter()
                            ->values()
                            ->all();
                    }),
                TextInput::make('validation')->nullable(),

                Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        Toggle::make('non_editable')->default(false),
                        Toggle::make('required')->default(false),
                        Toggle::make('show_on_invoice')->default(false),
                    ]),
                Section::make('Visibility conditions')
                    ->description('Control when this property is displayed based on other fields.')
                    ->schema([
                        Select::make('condition_mode')
                            ->label('Condition combination')
                            ->options([
                                'none' => 'Without conditions',
                                'all' => 'All conditions must match',
                                'any' => 'Any condition may match',
                            ])
                            ->default('none')
                            ->selectablePlaceholder(false)
                            ->reactive()
                            ->helperText('Select how the conditions should be evaluated.'),
                        Repeater::make('condition_rules')
                            ->label('Conditions')
                            ->columns(1)
                            ->default([])
                            ->defaultItems(0)
                            ->collapsed(false)
                            ->visible(fn (callable $get) => ($get('condition_mode') ?? 'none') !== 'none')
                            ->addActionLabel('Add condition')
                            ->schema([
                                Select::make('property_key')
                                    ->label('Property')
                                    ->options(fn () => self::getConditionPropertyOptions())
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Select a property'),
                                Select::make('operator')
                                    ->label('Operator')
                                    ->options([
                                        'equals' => 'Equals',
                                        'not_equals' => 'Does not equal',
                                        'in' => 'Is in list',
                                        'not_in' => 'Is not in list',
                                        'filled' => 'Is filled',
                                        'blank' => 'Is blank',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->helperText('Choose how the selected property should be evaluated.'),
                                Select::make('value_picker')
                                    ->label('Value')
                                    ->statePath('value')
                                    ->native(false)
                                    ->searchable()
                                    ->options(fn (callable $get) => self::getConditionValueOptions($get('property_key')))
                                    ->visible(fn (callable $get) => self::hasConditionValueOptions($get('property_key')) && in_array($get('operator'), ['equals', 'not_equals'], true))
                                    ->placeholder('Select a value'),
                                TextInput::make('value')
                                    ->label('Value')
                                    ->placeholder('Enter a value')
                                    ->datalist(fn (callable $get) => array_values(self::getConditionValueOptions($get('property_key'))))
                                    ->hidden(fn (callable $get) => in_array($get('operator'), ['equals', 'not_equals'], true) && self::hasConditionValueOptions($get('property_key')))
                                    ->dehydrateStateUsing(fn ($state, callable $get) => in_array($get('operator'), ['equals', 'not_equals'], true) && is_string($state) && trim($state) !== '' ? trim($state) : null),
                                Select::make('values_picker')
                                    ->label('Values')
                                    ->statePath('values')
                                    ->native(false)
                                    ->multiple()
                                    ->searchable()
                                    ->options(fn (callable $get) => self::getConditionValueOptions($get('property_key')))
                                    ->visible(fn (callable $get) => in_array($get('operator'), ['in', 'not_in'], true) && self::hasConditionValueOptions($get('property_key')))
                                    ->placeholder('Select values'),
                                TagsInput::make('values')
                                    ->label('Values')
                                    ->placeholder('Add values and press enter')
                                    ->suggestions(fn (callable $get) => array_values(self::getConditionValueOptions($get('property_key'))))
                                    ->hidden(fn (callable $get) => ! in_array($get('operator'), ['in', 'not_in'], true) || self::hasConditionValueOptions($get('property_key')))
                                    ->dehydrateStateUsing(function ($state, callable $get) {
                                        if (! in_array($get('operator'), ['in', 'not_in'], true)) {
                                            return [];
                                        }

                                        if (! is_array($state)) {
                                            return [];
                                        }

                                        return collect($state)
                                            ->map(function ($value) {
                                                if (! is_string($value)) {
                                                    return null;
                                                }

                                                $trimmed = trim($value);

                                                return $trimmed === '' ? null : $trimmed;
                                            })
                                            ->filter()
                                            ->values()
                                            ->all();
                                    }),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Property')
                    ->description(function (CustomProperty $record): ?HtmlString {
                        $rawDescription = trim((string) ($record->description ?? ''));

                        if ($rawDescription === '') {
                            return null;
                        }

                        $wrapped = wordwrap($rawDescription, 80, "\n");
                        $lines = preg_split('/\r\n|\r|\n/', $wrapped) ?: [];

                        if ($lines === []) {
                            return new HtmlString(e($rawDescription));
                        }

                        $hasOverflow = count($lines) > 2;
                        $lines = array_slice($lines, 0, 2);

                        if ($hasOverflow) {
                            $lastIndex = count($lines) - 1;
                            $lines[$lastIndex] = rtrim($lines[$lastIndex]) . '...';
                        }

                        $escapedLines = array_map(static fn (string $line): string => e($line), $lines);

                        return new HtmlString(implode('<br>', $escapedLines));
                    })
                    ->wrap(),
                TextColumn::make('key'),
                TextColumn::make('type')->formatStateUsing(fn ($state) => str($state)->title()),
                ToggleColumn::make('non_editable'),
                ToggleColumn::make('required'),
                ToggleColumn::make('show_on_invoice'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                //
            ])
            ->recordActions([
                TableAction::make('move_up')
                    ->label('Move up')
                    ->icon('heroicon-o-arrow-up')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Move property up')
                    ->action(function (CustomProperty $record) {
                        $record->moveOrderUp();
                        $record->refresh();
                    })
                    ->visible(fn (CustomProperty $record): bool => $record->canMoveUp()),
                TableAction::make('move_down')
                    ->label('Move down')
                    ->icon('heroicon-o-arrow-down')
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Move property down')
                    ->action(function (CustomProperty $record) {
                        $record->moveOrderDown();
                        $record->refresh();
                    })
                    ->visible(fn (CustomProperty $record): bool => $record->canMoveDown()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])->defaultGroup('model');
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
            'index' => ListCustomProperty::route('/'),
            'create' => CreateCustomProperty::route('/create'),
            'edit' => EditCustomProperty::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data = static::prepareConditionData($data);

        return static::prepareSortOrder($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data = static::prepareConditionData($data);

        return static::prepareSortOrder($data);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * @return array<string, string>
     */
    protected static function getConditionPropertyOptions(): array
    {
        if (self::$conditionPropertyOptionsCache !== null) {
            return self::$conditionPropertyOptionsCache;
        }

        return self::$conditionPropertyOptionsCache = CustomProperty::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'key')
            ->toArray();
    }

    protected static function getConditionValueOptions(?string $propertyKey): array
    {
        if (! is_string($propertyKey) || trim($propertyKey) === '') {
            return [];
        }

        if (array_key_exists($propertyKey, self::$conditionValueOptionsCache)) {
            return self::$conditionValueOptionsCache[$propertyKey];
        }

        $property = CustomProperty::query()
            ->where('key', $propertyKey)
            ->first(['allowed_values']);

        if (! $property || ! is_array($property->allowed_values)) {
            return self::$conditionValueOptionsCache[$propertyKey] = [];
        }

        $options = collect($property->allowed_values)
            ->map(function ($value) {
                if (! is_scalar($value)) {
                    return null;
                }

                $normalized = trim((string) $value);

                return $normalized === '' ? null : $normalized;
            })
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($value) => [$value => $value])
            ->toArray();

        return self::$conditionValueOptionsCache[$propertyKey] = $options;
    }

    protected static function hasConditionValueOptions(?string $propertyKey): bool
    {
        return self::getConditionValueOptions($propertyKey) !== [];
    }

    protected static function prepareConditionData(array $data): array
    {
        $mode = $data['condition_mode'] ?? 'none';

        if ($mode === 'none') {
            $data['condition_rules'] = null;

            return $data;
        }

        if (! isset($data['condition_rules']) || ! is_array($data['condition_rules'])) {
            $data['condition_rules'] = null;

            return $data;
        }

        $data['condition_rules'] = collect($data['condition_rules'])
            ->map(function ($condition) {
                if (! is_array($condition)) {
                    return null;
                }

                $propertyKey = isset($condition['property_key']) ? trim((string) $condition['property_key']) : '';

                if ($propertyKey === '') {
                    return null;
                }

                $operator = $condition['operator'] ?? null;

                if (! is_string($operator) || trim($operator) === '') {
                    return null;
                }

                $operator = strtolower($operator);

                if (! in_array($operator, ['equals', 'not_equals', 'in', 'not_in', 'filled', 'blank'], true)) {
                    return null;
                }

                $value = $condition['value'] ?? null;
                $values = $condition['values'] ?? [];

                if (in_array($operator, ['equals', 'not_equals'], true)) {
                    $value = is_string($value) && trim($value) !== '' ? trim($value) : null;
                    $values = [];
                } elseif (in_array($operator, ['in', 'not_in'], true)) {
                    if (! is_array($values)) {
                        $values = is_string($values) ? preg_split('/[,;|]+/', $values) : [];
                    }

                    $values = collect($values)
                        ->map(fn ($item) => is_scalar($item) ? trim((string) $item) : null)
                        ->filter(fn ($item) => $item !== null && $item !== '')
                        ->unique()
                        ->values()
                        ->all();

                    $value = null;
                } else {
                    $value = null;
                    $values = [];
                }

                return [
                    'property_key' => $propertyKey,
                    'operator' => $operator,
                    'value' => $value,
                    'values' => $values,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        if ($data['condition_rules'] === []) {
            $data['condition_rules'] = null;
        }

        return $data;
    }

    protected static function prepareSortOrder(array $data): array
    {
        $model = isset($data['model']) && is_string($data['model'])
            ? trim($data['model'])
            : null;

        if ($model === null || $model === '') {
            unset($data['sort_order']);

            return $data;
        }

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = CustomProperty::nextSortOrderForModel($model);

            return $data;
        }

        if (! is_numeric($data['sort_order'])) {
            $data['sort_order'] = CustomProperty::nextSortOrderForModel($model);

            return $data;
        }

        $data['sort_order'] = max(0, (int) $data['sort_order']);

        return $data;
    }
}
