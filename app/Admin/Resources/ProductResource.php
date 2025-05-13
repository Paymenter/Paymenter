<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ProductResource\Pages;
use App\Classes\FilamentInput;
use App\Helpers\ExtensionHelper;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Server;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'ri-instance-line';

    protected static ?string $activeNavigationIcon = 'ri-instance-fill';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),
                                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('stock')->integer()->nullable(),
                                Forms\Components\TextInput::make('per_user_limit')->integer()->nullable(),
                                Forms\Components\Select::make('allow_quantity')->options([
                                    'disabled' => 'No',
                                    'separated' => 'Separated',
                                    'combined' => 'Combined',
                                ])->default('separated')
                                    ->required(),
                                Forms\Components\Textarea::make('email_template')
                                    ->hint('This snippet will be used in the email template.')
                                    ->nullable(),
                                Forms\Components\Checkbox::make('hidden')
                                    ->label('Hide product')
                                    ->hint('Hide the product from the client area.'),

                                Forms\Components\RichEditor::make('description')->nullable()->columnSpanFull(),
                                Forms\Components\FileUpload::make('image')->label('Image')->nullable()->acceptedFileTypes(['image/*']),
                                Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm(fn (Form $form) => CategoryResource::form($form))
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Pricing')
                            ->schema([self::plan()]),

                        Tabs\Tab::make('Upgrades')
                            ->schema([
                                // Select input for the products this product can upgrade to (hasmany relationship)
                                Forms\Components\Select::make('upgrades')
                                    ->label('Upgrades')
                                    ->relationship('upgrades', 'name', ignoreRecord: true)
                                    ->multiple()
                                    ->preload()
                                    ->placeholder('Select the products that this product can upgrade to'),
                            ]),

                        Tabs\Tab::make('Server')
                            ->schema([
                                Forms\Components\Select::make('server_id')
                                    ->relationship('server', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->hintAction(
                                        Action::make('refresh')
                                            ->label('Refresh')
                                            ->action(fn () => Cache::set('product_config', null, 0))
                                            ->hidden(fn (Get $get) => $get('server_id') === null)
                                    )
                                    ->live(),

                                Grid::make('settings')
                                    ->hidden(fn (Get $get) => $get('server_id') === null)
                                    ->columns(2)
                                    ->schema(
                                        function (Get $get) {
                                            $server = $get('server_id');
                                            if ($server == null) {
                                                return [];
                                            }
                                            $settings = [];

                                            try {
                                                foreach (ExtensionHelper::getProductConfigOnce(Server::findOrFail($server), $get('settings')) as $setting) {
                                                    // Easier to use dot notation for settings
                                                    $setting['name'] = 'settings.' . $setting['name'];
                                                    $settings[] = FilamentInput::convert($setting);
                                                }
                                            } catch (\Exception $e) {
                                                $settings[] = Forms\Components\Placeholder::make('error')->content($e->getMessage());
                                            }

                                            return $settings;
                                        }
                                    ),

                            ]),
                    ]),
            ])->columns(1);
    }

    public static function plan()
    {
        return Forms\Components\Repeater::make('plan')
            ->addActionLabel('Add new plan')
            ->relationship('plans')
            ->name('name')
            ->reorderable()
            ->cloneable()
            ->collapsible()
            ->collapsed()
            ->orderColumn()
            ->defaultItems(1)
            ->minItems(1)
            ->columns(2)
            ->deleteAction(function (Action $action) {
                $action->before(function (?Product $record, $state, Action $action, array $arguments) {
                    if (!$record) {
                        return;
                    }
                    $key = $arguments['item'];
                    if (!isset($state[$key]['id'])) {
                        return;
                    }
                    $plan = $record->plans()->find($state[$key]['id']);
                    if ($plan->services()->count() > 0) {
                        Notification::make()
                            ->title('Whoops!')
                            ->body('You cannot delete this plan because it is being used by one or more services.')
                            ->danger()
                            ->send();
                        $action->cancel();
                    }
                });
            })
            ->itemLabel(fn (array $state) => $state['name'])
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'free' => 'Free',
                        'one-time' => 'One Time',
                        'recurring' => 'Recurring',
                    ])
                    ->required()
                    ->live(debounce: 300)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if ($state === 'free') {
                            $set('every', null);
                            $set('price', 0);
                        }
                    })
                    ->placeholder('Select the type of the price')
                    ->default('free'),

                Forms\Components\TextInput::make('billing_period')
                    ->required()
                    ->label('Time Interval')
                    ->default(1)
                    ->hidden(fn (Get $get) => $get('type') !== 'recurring'),

                Forms\Components\Select::make('billing_unit')
                    ->options([
                        'day' => 'Day',
                        'week' => 'Week',
                        'month' => 'Month',
                        'year' => 'Year',
                    ])
                    ->label('Billing period')
                    ->required()
                    ->default('month')
                    ->hidden(fn (Get $get) => $get('type') !== 'recurring'),
                Forms\Components\Repeater::make('pricing')
                    ->hidden(fn (Get $get) => $get('type') === 'free')
                    ->columns(3)
                    ->addActionLabel('Add new price')
                    ->reorderable(false)
                    ->relationship('prices')
                    ->columnSpanFull()
                    ->maxItems(Currency::count())
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state) => $state['currency_code'])
                    ->schema([
                        Forms\Components\Select::make('currency_code')
                            ->options(function (Get $get, ?string $state) {
                                $pricing = collect($get('../../pricing'))->pluck('currency_code');
                                if ($state !== null) {
                                    $pricing = $pricing->filter(function ($code) use ($state) {
                                        return $code !== $state;
                                    });
                                }
                                $pricing = $pricing->filter(function ($code) {
                                    return $code !== null;
                                });

                                return Currency::whereNotIn('code', $pricing)->pluck('code', 'code');
                            })
                            ->live()
                            ->default(config('settings.default_currency'))
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->label('Price')
                            // Suffix based on chosen currency
                            ->prefix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->prefix)
                            ->suffix(fn (Get $get) => Currency::where('code', $get('currency_code'))->first()?->suffix)
                            ->live(onBlur: true)
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->numeric()
                            ->minValue(0)
                            ->hidden(fn (Get $get) => $get('type') === 'free'),
                        Forms\Components\TextInput::make('setup_fee')
                            ->label('Setup fee')
                            ->live(onBlur: true)
                            ->mask(RawJs::make(
                                <<<'JS'
                                    $money($input, '.', '', 2)
                                JS
                            ))
                            ->numeric()
                            ->minValue(0)
                            ->hidden(fn (Get $get) => $get('type') === 'free'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('category.name')->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('sort', 'asc');
            })
            ->reorderable('sort')
            ->defaultGroup('category.name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
